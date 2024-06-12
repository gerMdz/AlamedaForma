<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Inicio;
use App\State\InicioStateProvider;



final class InicioApi
{
    final public const DESCRIPTION = 'Recuperar el texto, contenido y términos y condiciones del inicio.';

    public string $id;
    public string $title;
    public string $content;
    public string $terms;
    public string $organization;

}