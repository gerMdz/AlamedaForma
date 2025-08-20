<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\PersonalController;
use App\Controller\Api\PersonalFormationController;
use App\Entity\Personales;
use App\Entity\PersonalFormation;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;

#[ApiResource(
    shortName: 'PersonalFormation',
    operations: [
        new Get (
            uriTemplate: '/personal-formation',
            openapi: new OpenApiOperation(summary: self::DESCRIPTION),
            description: self::DESCRIPTION,
        ),
        new GetCollection(
            description: self::DESCRIPTION,
        ),
        new Post(
            uriTemplate: '/personal-formation',
            controller: PersonalFormationController::class,

            openapi: new OpenApiOperation(summary: self::DESCRIPTION),
            description: self::DESCRIPTION,
            extraProperties: [
                'entity' => PersonalFormation::class,
            ],
        )
    ]
)]
final class PersonalFormationResource
{
    final public const DESCRIPTION = 'Recuperar la relación entre personas y formación.';

    public string $percentDon;
    public string $commentDon;
    public string $don;
    public string $person;


    public function __toString(): string
    {
        return $this->don . ' ' . $this->percentDon;
    }

}