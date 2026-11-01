<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Project;

#[ApiResource(
    shortName: 'Project',
    operations: [
        new GetCollection(),
    ],
    security: 'is_granted("ROLE_USER")',
    stateOptions: new Options(entityClass: Project::class)
)]
class ProjectApi
{
    public ?int $id = null;

    public ?string $title = null;

    public ?string $description = null;
}
