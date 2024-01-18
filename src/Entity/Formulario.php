<?php

namespace App\Entity;

use App\Repository\FormularioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormularioRepository::class)]
class Formulario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'formularios')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Forma $forma = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pregunta = null;

    #[ORM\Column(length: 510)]
    private ?string $value = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getForma(): ?Forma
    {
        return $this->forma;
    }

    public function setForma(?Forma $forma): static
    {
        $this->forma = $forma;

        return $this;
    }

    public function getPregunta(): ?string
    {
        return $this->pregunta;
    }

    public function setPregunta(?string $pregunta): static
    {
        $this->pregunta = $pregunta;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }
}
