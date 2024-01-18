<?php

namespace App\Entity;

use App\Repository\FormaRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormaRepository::class)]
class Forma
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Personales $persona = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'forma', targetEntity: Formulario::class)]
    private Collection $formularios;

    public function __construct()
    {
        $this->formularios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPersona(): ?Personales
    {
        return $this->persona;
    }

    public function setPersona(?Personales $persona): static
    {
        $this->persona = $persona;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Formulario>
     */
    public function getFormularios(): Collection
    {
        return $this->formularios;
    }

    public function addFormulario(Formulario $formulario): static
    {
        if (!$this->formularios->contains($formulario)) {
            $this->formularios->add($formulario);
            $formulario->setForma($this);
        }

        return $this;
    }

    public function removeFormulario(Formulario $formulario): static
    {
        if ($this->formularios->removeElement($formulario)) {
            // set the owning side to null (unless already changed)
            if ($formulario->getForma() === $this) {
                $formulario->setForma(null);
            }
        }

        return $this;
    }
}
