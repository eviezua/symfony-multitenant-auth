<?php

namespace App\Tests;

use ApiPlatform\Test\ApiTestCase;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AuthTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testSuccessLogin(): void
    {
        $client = static::createClient();

        UserFactory::createOne([
            'email' => 'user@example.com',
            'password' => 'password'
        ]);

        $client->request('POST', '/api/v1/login', [
            'json' => [
                'email' => 'user@example.com',
                'password' => 'password',
            ]
        ]);

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
        $this->assertIsString($data['token']);
    }

    public function testFailedLogin(): void
    {
        $client = static::createClient();

        UserFactory::createOne([
            'email' => 'user@example.com',
            'password' => 'drowssap'
        ]);

        $client->request('POST', '/api/v1/login', [
            'json' => [
                'email' => 'user@example.com',
                'password' => 'password',
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains(['message' => 'Invalid credentials.']);
    }
}
