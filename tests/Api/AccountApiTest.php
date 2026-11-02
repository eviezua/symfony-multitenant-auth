<?php

namespace App\Tests\Api;

use ApiPlatform\Test\ApiTestCase;
use App\Entity\User;
use App\Factory\AccountFactory;
use App\Factory\UserFactory;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AccountApiTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testGetAccountsList(): void
    {
        $current_account = AccountFactory::createOne();

        $accounts = AccountFactory::createMany(5);

        AccountFactory::createMany(5);

        $user = UserFactory::createOne([
            'accounts' => [$current_account, ...$accounts]
        ]);

        $this->sendGetProjectsRequest($user);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/v1/contexts/Account',
            '@type' => 'Collection',
            'totalItems' => 6,
        ]);
    }

    private function sendGetProjectsRequest(User $user): ResponseInterface
    {
        $client = static::createClient();

        $jwtManager = static::getContainer()->get(JWTTokenManagerInterface::class);
        $token = $jwtManager->create($user);

        $headers = [
            'Authorization' => 'Bearer ' . $token,
        ];

        return $client->request('GET', '/api/v1/accounts', [
            'headers' => $headers,
        ]);
    }
}
