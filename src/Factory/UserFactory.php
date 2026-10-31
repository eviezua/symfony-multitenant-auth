<?php

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<User>
 */
final class UserFactory extends PersistentObjectFactory
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    )
    {
        parent::__construct();
    }

    #[\Override]
    public static function class(): string
    {
        return User::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'email' => self::faker()->text(180),
            'password' => self::faker()->text(255),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this->afterInstantiate(function(User $user) {
            if ($user->getPassword()) {
                $user->setPassword(
                    $this->passwordHasher->hashPassword($user, $user->getPassword())
                );
            }
        });
    }
}
