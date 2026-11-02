<?php

namespace App\Tests\Entity;

use App\Entity\Account;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserEntityTest extends TestCase
{
    public function testAddAndRemoveAccount(): void
    {
        $user = new User();
        $account = new Account();

        $this->assertCount(0, $user->getAccounts());

        $user->addAccount($account);
        $this->assertCount(1, $user->getAccounts());
        $this->assertTrue($user->getAccounts()->contains($account));

        $user->removeAccount($account);
        $this->assertCount(0, $user->getAccounts());
        $this->assertFalse($user->getAccounts()->contains($account));
    }

    public function testGetRolesIncludesRoleUser(): void
    {
        $user = new User();
        $roles = $user->getRoles();

        $this->assertContains('ROLE_USER', $roles);
    }
}
