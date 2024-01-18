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
}
