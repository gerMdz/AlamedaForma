<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\PersonalController;
use App\Entity\Personales;

#[ApiResource(
    shortName: 'Personal',
    operations: [
        new Get (
            uriTemplate: '/personal',
            openapiContext: ['summary' => self::DESCRIPTION],
            description: self::DESCRIPTION,
        ),
        new GetCollection(
            description: self::DESCRIPTION,
        ),
        new Post(
            uriTemplate: '/personal',
            controller: PersonalController::class,

            openapiContext: ['summary' => self::DESCRIPTION],
            description: self::DESCRIPTION,
            extraProperties: [
                'entity' => Personales::class,
            ],
        )
    ]
)]
final class PersonalResource
{
    final public const DESCRIPTION = 'Recuperar las preguntas de datos personales.';

    public string $name;
    public string $apellido;
    public string $email;
    public string $phone;
    public string $point;
    public string $id;

    public function __toString(): string
    {
        return $this->name . ' ' . $this->apellido;
    }

}