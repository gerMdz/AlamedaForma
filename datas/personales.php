<?php
// src/Entity/Personales.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Symfony\Bridge\Doctrine\Types\UuidV4Type;

#[ORM\Entity(repositoryClass: 'App\Repository\PersonalesRepository')]
class Personales
{
    #[ORM\Id]
    #[ORM\Column(type: UuidV4Type::NAME)]
    #[ORM\GeneratedValue(strategy: UuidV4Generator::NAME)]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $puntoId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $userName;

    #[ORM\Column(type: 'string', length: 255)]
    private string $userLastName;

    #[ORM\Column(type: 'string', length: 255)]
    private string $userEmailAddress;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $anotherField = null;

    // Se asume el uso de PHP 8.0 o superior para los tipos de propiedad

    // getters and setters...
}

