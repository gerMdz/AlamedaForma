<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Inicio;
use App\Entity\Personalidad;
use App\State\InicioStateProvider;
use App\State\PersonalidadStateProvider;


#[ApiResource(
    shortName: 'Inicio',
    operations: [
        new GetCollection(
            uriTemplate: '/inicio',
            openapiContext: ['summary' => self::DESCRIPTION],
            description: self::DESCRIPTION,
            provider: InicioStateProvider::class,
            extraProperties: [
                'entity' => Inicio::class,
            ],
        ),
    ]
)]
final class InicioResource
{
    final public const DESCRIPTION = 'Recuperar el texto, contenido y términos y condiciones del inicio.';

    public string $id;
    public string $title;
    public string $content;
    public string $terms;
    public string $organization;

}