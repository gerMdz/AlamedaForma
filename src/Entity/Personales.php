<?php

namespace App\Entity;

use AllowDynamicProperties;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\InicioRepository;
use App\Repository\PersonalesRepository;
use App\State\InicioStateProvider;
use App\State\PersonalStateProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;


#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/personales',
        )
    ],
    provider: PersonalStateProvider::class
)]
#[AllowDynamicProperties] #[ORM\Entity(repositoryClass: PersonalesRepository::class)]
class Personales
{
    #[ORM\Id]
    #[ORM\Column(type: 'custom_uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups('personal:read')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['personal:read', 'personal:create'])]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $apellido = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $observaciones = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $point = null;

    /**
     * @var Collection<int, PersonalFormation>
     */
    #[ORM\OneToMany(mappedBy: 'person', targetEntity: PersonalFormation::class, orphanRemoval: true)]
    private Collection $personalFormations;

    public function __construct()
    {
        $this->personalFormations = new ArrayCollection();
    }

    public function __toString(): string
    {
     return $this->nombre . ' ' . $this->apellido;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(string $apellido): self
    {
        $this->apellido = $apellido;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(string $observaciones): self
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPoint(): ?string
    {
        return $this->point;
    }

    public function setPoint(?string $point): static
    {
        $this->point = $point;

        return $this;
    }

    /**
     * @return Collection<int, PersonalFormation>
     */
    public function getPersonalFormations(): Collection
    {
        return $this->personalFormations;
    }

    public function addPersonalFormation(PersonalFormation $personalFormation): static
    {
        if (!$this->personalFormations->contains($personalFormation)) {
            $this->personalFormations->add($personalFormation);
            $personalFormation->setPerson($this);
        }

        return $this;
    }

    public function removePersonalFormation(PersonalFormation $personalFormation): static
    {
        if ($this->personalFormations->removeElement($personalFormation)) {
            // set the owning side to null (unless already changed)
            if ($personalFormation->getPerson() === $this) {
                $personalFormation->setPerson(null);
            }
        }

        return $this;
    }
}
