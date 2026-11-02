<?php

namespace App\Tests\Entity;

use App\Entity\Account;
use App\Entity\Project;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class AccountEntityTest extends TestCase
{
    public function testAddAndRemoveUser(): void
    {
        $account = new Account();
        $user = new User();

        $this->assertCount(0, $account->getUsers());

        $account->addUser($user);
        $this->assertCount(1, $account->getUsers());
        $this->assertTrue($account->getUsers()->contains($user));

        $account->addUser($user);
        $this->assertCount(1, $account->getUsers());

        $account->removeUser($user);
        $this->assertCount(0, $account->getUsers());
        $this->assertFalse($account->getUsers()->contains($user));
    }

    public function testAddAndRemoveProject(): void
    {
        $account = new Account();
        $project = new Project();

        $this->assertCount(0, $account->getProjects());

        $account->addProject($project);
        $this->assertCount(1, $account->getProjects());
        $this->assertTrue($account->getProjects()->contains($project));
        $this->assertSame($account, $project->getAccount());

        $account->removeProject($project);
        $this->assertCount(0, $account->getProjects());
        $this->assertFalse($account->getProjects()->contains($project));
    }
}
