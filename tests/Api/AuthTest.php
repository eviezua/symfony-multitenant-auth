<?php

namespace App\Tests\Api;

use ApiPlatform\Test\ApiTestCase;
use ApiPlatform\Test\Client;
use App\Factory\AccountFactory;
use App\Factory\UserFactory;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AuthTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testSuccessLogin(): void
    {
        $client = static::createClient();

        $account = AccountFactory::createOne(['name' => 'Company Account']);
        $this->createUser([$account]);

        $this->requestLogin($client, 'user@example.com', 'password');
        $this->assertResponseIsSuccessful();

        $data = $client->getResponse()->toArray();
        $this->assertIsAuthPayload($data);
        $this->assertSame($account->getId(), $data['default_account_id']);
    }

    public function testSuccessRefreshToken(): void
    {
        $client = static::createClient();
        $refreshToken = $this->obtainRefreshToken($client);

        $refreshResponse = $this->requestRefreshToken($client, $refreshToken);
        $this->assertResponseIsSuccessful();

        $this->assertIsAuthPayload($refreshResponse->toArray());
    }

    public function testFailedRefreshToken(): void
    {
        $client = static::createClient();
        $refreshToken = $this->obtainRefreshToken($client);

        $this->requestRefreshToken($client, $refreshToken);
        $this->assertResponseIsSuccessful();

        $this->requestRefreshToken($client, $refreshToken);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains(['message' => 'JWT Refresh Token Not Found']);
    }

    public function testFailedLogin(): void
    {
        $client = static::createClient();

        $this->createUser();

        $this->requestLogin($client, 'user@example.com', 'drowssap');

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains(['message' => 'Invalid credentials.']);
    }

    private function obtainRefreshToken(
        Client $client,
        string $email = 'user@example.com',
        string $password = 'password'
    ): string {
        $this->createUser([], $email, $password);
        $response = $this->requestLogin($client, $email, $password);

        $this->assertResponseIsSuccessful();
        return $response->toArray()['refresh_token'];
    }

    private function createUser(
        array $accounts = [],
        string $email = 'user@example.com',
        string $password = 'password'
    ): void {
        UserFactory::createOne([
            'email' => $email,
            'password' => $password,
            'accounts' => $accounts,
        ]);
    }

    private function requestLogin(Client $client, string $email, string $password): ResponseInterface
    {
        return $client->request('POST', '/api/v1/login', [
            'json' => [
                'email' => $email,
                'password' => $password,
            ],
        ]);
    }

    private function requestRefreshToken(Client $client, string $refreshToken): ResponseInterface
    {
        return $client->request('POST', '/api/v1/token/refresh', [
            'json' => ['refresh_token' => $refreshToken],
        ]);
    }

    private function assertIsAuthPayload(array $data): void
    {
        $this->assertArrayHasKey('token', $data);
        $this->assertIsString($data['token']);

        $this->assertArrayHasKey('refresh_token', $data);
        $this->assertIsString($data['refresh_token']);
    }
}
