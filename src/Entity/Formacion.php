<?php

namespace App\Entity;

use AllowDynamicProperties;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\FormacionRepository;
use App\Repository\InicioRepository;
use App\State\FormationStateProvider;
use App\State\InicioStateProvider;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(

    operations: [
        new GetCollection(
            uriTemplate: '/formation',

        )
    ],
    provider: FormationStateProvider::class
)]
#[AllowDynamicProperties] #[ORM\Entity(repositoryClass: FormacionRepository::class)]
class Formacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $orden = null;

    #[ORM\Column(length: 510)]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    private ?string $identifier = null;

    #[ORM\Column(length: 255)]
    private ?string $parent = null;

    #[ORM\ManyToOne]
    private ?Dones $donAssociate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrden(): ?int
    {
        return $this->orden;
    }

    public function setOrden(int $orden): static
    {
        $this->orden = $orden;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function setParent(string $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    public function getDonAssociate(): ?Dones
    {
        return $this->donAssociate;
    }

    public function setDonAssociate(?Dones $donAssociate): static
    {
        $this->donAssociate = $donAssociate;

        return $this;
    }
}
