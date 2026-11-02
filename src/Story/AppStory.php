<?php

namespace App\Story;

use App\Factory\AccountFactory;
use App\Factory\ProjectFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'main')]
final class AppStory extends Story
{
    public function build(): void
    {
        $accountA = AccountFactory::createOne(['name' => 'Alpha Corp']);
        $accountB = AccountFactory::createOne(['name' => 'Beta Shared Hub']);
        $accountC = AccountFactory::createOne(['name' => 'Gamma LLC']);

        UserFactory::createOne([
            'email' => 'user1@example.com',
            'password' => 'password',
            'accounts' => [$accountA, $accountB]
        ]);

        UserFactory::createOne([
            'email' => 'user2@example.com',
            'password' => 'password',
            'accounts' => [$accountB, $accountC]
        ]);

        ProjectFactory::createMany(2, ['account' => $accountA]);
        ProjectFactory::createMany(3, ['account' => $accountB]);
        ProjectFactory::createMany(1, ['account' => $accountC]);
    }
}
