<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\PersonalController;
use App\Entity\Formacion;
use App\Entity\Personales;
use Doctrine\ORM\Mapping as ORM;


final class FormationResource
{
    final public const DESCRIPTION = 'Recupera las preguntas para el formulario de formación.';

    public string $id;
        public string $orden;
        public string $description;
        public string $identifier;
        public string $parent;
        public ?string $don;

}