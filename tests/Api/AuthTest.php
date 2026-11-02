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

        $this->assertArrayHasKey('token', $data);
        $this->assertIsString($data['token']);

        $this->assertArrayHasKey('default_account_id', $data);
        $this->assertSame($account->getId(), $data['default_account_id']);
    }

    public function testFailedLogin(): void
    {
        $client = static::createClient();

        $this->createUser([]);

        $this->requestLogin($client, 'user@example.com', 'drowssap');

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains(['message' => 'Invalid credentials.']);
    }

    private function createUser(
        array $accounts,
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
}
