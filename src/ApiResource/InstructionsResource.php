<?php

namespace App\ApiResource;


use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Instructions;
use App\Entity\Personalidad;
use App\State\InstructionsStateProvider;
use App\State\PersonalidadStateProvider;

#[ApiResource(
    shortName: 'Instructions',
    operations: [
        new GetCollection(
            uriTemplate: '/instructions',
            openapiContext: ['summary' => self::DESCRIPTION],
            description: self::DESCRIPTION,
            provider: InstructionsStateProvider::class,
            extraProperties: [
                'entity' => Instructions::class,
            ],
        ),
    ]
)]
class InstructionsResource
{
    final public const DESCRIPTION = 'Recuperar las instrucciones para el uso del formulario.';

    public int $id;
    public string $title;
    public string $content;
    public bool $enabled;
    public string $organization;
}