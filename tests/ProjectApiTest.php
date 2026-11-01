<?php

namespace App\Tests;

use ApiPlatform\Test\ApiTestCase;
use App\Entity\User;
use App\Factory\AccountFactory;
use App\Factory\ProjectFactory;
use App\Factory\UserFactory;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProjectApiTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testGetProjectsList(): void
    {
        [$account, $user] = $this->createAccountWithProjects();

        $this->sendGetProjectsRequest($user, $account->getId());

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/v1/contexts/Project',
            '@type' => 'Collection',
            'totalItems' => 3,
        ]);
    }

    public function testGetOtherUserProjectsList(): void
    {
        $this->createAccountWithProjects();

        $otherUser = UserFactory::createOne();

        $this->sendGetProjectsRequest($otherUser);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/v1/contexts/Project',
            '@type' => 'Collection',
            'totalItems' => 0,
        ]);
    }

    public function testGetOtherUserProjectsListById(): void
    {
        [$account] = $this->createAccountWithProjects();
        $otherUser = UserFactory::createOne();

        $this->sendGetProjectsRequest($otherUser, $account->getId());

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/v1/contexts/Project',
            '@type' => 'Collection',
            'totalItems' => 0,
        ]);
    }

    private function createAccountWithProjects(): array
    {
        $account = AccountFactory::createOne(['name' => 'Test Account']);

        ProjectFactory::createMany(3, [
            'account' => $account,
        ]);

        $user = UserFactory::createOne([
            'accounts' => [$account],
        ]);

        return [$account, $user];
    }

    private function sendGetProjectsRequest(User $user, ?int $accountId = null): ResponseInterface
    {
        $client = static::createClient();

        $jwtManager = static::getContainer()->get(JWTTokenManagerInterface::class);
        $token = $jwtManager->create($user);

        $headers = [
            'Authorization' => 'Bearer ' . $token,
        ];

        if (null !== $accountId) {
            $headers['Account-ID'] = $accountId;
        }

        return $client->request('GET', '/api/v1/projects', [
            'headers' => $headers,
        ]);
    }
}
