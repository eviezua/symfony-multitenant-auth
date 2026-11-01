<?php

namespace App\Factory;

use App\Entity\Project;
use Override;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Project>
 */
final class ProjectFactory extends PersistentObjectFactory
{
    public function __construct()
    {
    }

    #[Override]
    public static function class(): string
    {
        return Project::class;
    }

    #[Override]
    protected function defaults(): array|callable
    {
        return [
            'account' => AccountFactory::new(),
            'title' => self::faker()->sentence(),
            'description' => self::faker()->paragraph(),
        ];
    }

    #[Override]
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(Project $project): void {})
            ;
    }
}
