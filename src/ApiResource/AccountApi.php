<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Account;

#[ApiResource(
    shortName: 'Account',
    operations: [
        new GetCollection(),
    ],
    security: 'is_granted("ROLE_USER")',
    stateOptions: new Options(entityClass: Account::class)
)]
class AccountApi
{
    public ?int $id = null;

    public ?string $name = null;
}
