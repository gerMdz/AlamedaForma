<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Doctrine\CustomUuidType;
use App\Repository\OrganizationRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]

class Organization
{
    #[ORM\Id]
    #[ORM\Column(type: 'custom_uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $isDisabled = null;

    #[ORM\OneToOne(mappedBy: 'organization', cascade: ['persist', 'remove'])]
    private ?Inicio $inicio = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 100)]
    private ?string $identifier = null;

    #[ORM\OneToOne(mappedBy: 'organization', cascade: ['persist', 'remove'])]
    private ?Instructions $instructions = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_request = null;


    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->isDisabled = false;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isDisabled(): ?bool
    {
        return $this->isDisabled;
    }

    public function setDisabled(bool $isDisabled = false): static
    {
        $this->isDisabled = $isDisabled;

        return $this;
    }

    public function getInicio(): ?Inicio
    {
        return $this->inicio;
    }

    public function setInicio(?Inicio $inicio): static
    {
        // unset the owning side of the relation if necessary
        if ($inicio === null && $this->inicio !== null) {
            $this->inicio->setOrganization(null);
        }

        // set the owning side of the relation if necessary
        if ($inicio !== null && $inicio->getOrganization() !== $this) {
            $inicio->setOrganization($this);
        }

        $this->inicio = $inicio;

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

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

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

    public function getInstructions(): ?Instructions
    {
        return $this->instructions;
    }

    public function setInstructions(?Instructions $instructions): static
    {
        // unset the owning side of the relation if necessary
        if ($instructions === null && $this->instructions !== null) {
            $this->instructions->setOrganization(null);
        }

        // set the owning side of the relation if necessary
        if ($instructions !== null && $instructions->getOrganization() !== $this) {
            $instructions->setOrganization($this);
        }

        $this->instructions = $instructions;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getEmailRequest(): ?string
    {
        return $this->email_request;
    }

    public function setEmailRequest(?string $email_request): static
    {
        $this->email_request = $email_request;

        return $this;
    }
}
