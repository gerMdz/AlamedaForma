<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Personalidad;
use App\State\PersonalidadStateProvider;

#[ApiResource(
    shortName: 'Personalidad',
    operations: [
        new GetCollection(
            uriTemplate: '/personalidad',
            openapiContext: ['summary' => self::DESCRIPTION],
            description: self::DESCRIPTION,
            provider: PersonalidadStateProvider::class,
            extraProperties: [
                'entity' => Personalidad::class,
            ],
        ),
    ]
)]
final class PersonalResource
{
    final public const DESCRIPTION = 'Recuperar las preguntas de personalidad.';

    public int $id;
    public string $D;
    public string $I;
    public string $S;
    public string $C;

}