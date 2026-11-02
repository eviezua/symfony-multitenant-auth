<?php

namespace App\Tests\Api;

use ApiPlatform\Test\ApiTestCase;
use App\Entity\Account;
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
        $account = $this->createAccountWithProjects(3);

        $user = UserFactory::createOne([
            'accounts' => [$account]
        ]);

        $this->sendGetProjectsRequest($user, $account->getId());

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/v1/contexts/Project',
            '@type' => 'Collection',
            'totalItems' => 3,
        ]);
    }

    public function testSwitchAccounts(): void
    {
        $emptyAccount = $this->createAccountWithProjects(0);
        $accountWithProjects = $this->createAccountWithProjects(3);

        $user = UserFactory::createOne([
            'accounts' => [$emptyAccount, $accountWithProjects]
        ]);

        $this->sendGetProjectsRequest($user, $emptyAccount->getId());

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/v1/contexts/Project',
            '@type' => 'Collection',
            'totalItems' => 0,
        ]);

        $this->sendGetProjectsRequest($user, $accountWithProjects->getId());

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/v1/contexts/Project',
            '@type' => 'Collection',
            'totalItems' => 3,
        ]);
    }

    public function testGetSharedAccountProjectList(): void
    {
        $account = $this->createAccountWithProjects(3);
        UserFactory::createOne([
            'accounts' => [$account]
        ]);

        $otherUser = UserFactory::createOne([
            'accounts' => [$account]
        ]);

        $this->sendGetProjectsRequest($otherUser, $account->getId());

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/v1/contexts/Project',
            '@type' => 'Collection',
            'totalItems' => 3,
        ]);
    }

    public function testGetOtherUserProjectsList(): void
    {
        $account = $this->createAccountWithProjects(3);

        UserFactory::createOne([
            'accounts' => [$account]
        ]);

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
        $account = $this->createAccountWithProjects(3);
        UserFactory::createOne([
            'accounts' => [$account]
        ]);

        $otherUser = UserFactory::createOne();

        $this->sendGetProjectsRequest($otherUser, $account->getId());

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/v1/contexts/Project',
            '@type' => 'Collection',
            'totalItems' => 0,
        ]);
    }

    private function createAccountWithProjects(int $amount, string $name = 'Test Account'): Account
    {
        $account = AccountFactory::createOne(['name' => $name]);

        if ($amount > 0) {
            ProjectFactory::createMany($amount, [
                'account' => $account,
            ]);
        }

        return $account;
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
