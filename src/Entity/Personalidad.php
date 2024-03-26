<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PersonalidadRepository;
use App\State\PersonalidadStateProvider;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    provider: PersonalidadStateProvider::class,
)]
#[ORM\Entity(repositoryClass: PersonalidadRepository::class)]
class Personalidad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255)]
    public ?string $D = null;

    #[ORM\Column(length: 255)]
    public ?string $I = null;

    #[ORM\Column(length: 255)]
    public ?string $S = null;

    #[ORM\Column(length: 255)]
    public ?string $C = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getD(): ?string
    {
        return $this->D;
    }

    public function setD(string $D): static
    {
        $this->D = $D;

        return $this;
    }

    public function getI(): ?string
    {
        return $this->I;
    }

    public function setI(string $I): static
    {
        $this->I = $I;

        return $this;
    }

    public function getS(): ?string
    {
        return $this->S;
    }

    public function setS(string $S): static
    {
        $this->S = $S;

        return $this;
    }

    public function getC(): ?string
    {
        return $this->C;
    }

    public function setC(string $C): static
    {
        $this->C = $C;

        return $this;
    }
}
