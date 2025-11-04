<?php

namespace App\Entity;

use App\Repository\IntroExtroRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IntroExtroRepository::class)]
class IntroExtro
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $intro;

    #[ORM\Column(length: 255)]
    private string $extro;

    #[ORM\Column(length: 50, options: ['default' => '50/50'])]
    private string $mitad = '50/50';

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $activo = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntro(): string
    {
        return $this->intro;
    }

    public function setIntro(string $intro): self
    {
        $this->intro = $intro;
        return $this;
    }

    public function getExtro(): string
    {
        return $this->extro;
    }

    public function setExtro(string $extro): self
    {
        $this->extro = $extro;
        return $this;
    }

    public function getMitad(): string
    {
        return $this->mitad;
    }

    public function setMitad(string $mitad): self
    {
        $this->mitad = $mitad;
        return $this;
    }

    public function isActivo(): bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): self
    {
        $this->activo = $activo;
        return $this;
    }
}
