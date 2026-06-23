<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\PersonalAntecedentesRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: '/personal-antecedentes'),
        new Get(uriTemplate: '/personal-antecedentes/{id}'),
        new Post(uriTemplate: '/personal-antecedentes'),
        new Patch(uriTemplate: '/personal-antecedentes/{id}'),
        new Delete(uriTemplate: '/personal-antecedentes/{id}')
    ],
    normalizationContext: ['groups' => ['pa:read']],
    denormalizationContext: ['groups' => ['pa:write']]
)]
#[ORM\Entity(repositoryClass: PersonalAntecedentesRepository::class)]
#[ORM\Table(name: 'personal_antecedentes')]
#[ORM\HasLifecycleCallbacks]
class PersonalAntecedentes
{
    #[ORM\Id]
    #[ORM\Column(type: 'custom_uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['pa:read'])]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: Personales::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['pa:read', 'pa:write'])]
    private ?Personales $persona = null;

    #[ORM\ManyToOne(targetEntity: Antecedentes::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['pa:read', 'pa:write'])]
    private ?Antecedentes $antecedente = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['pa:read', 'pa:write'])]
    private ?string $respuesta = null;

    #[ORM\Column]
    #[Groups(['pa:read'])]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['pa:read'])]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['pa:read'])]
    private ?DateTimeImmutable $deletedAt = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?Uuid
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

    public function getAntecedente(): ?Antecedentes
    {
        return $this->antecedente;
    }

    public function setAntecedente(?Antecedentes $antecedente): static
    {
        $this->antecedente = $antecedente;

        return $this;
    }

    public function getRespuesta(): ?string
    {
        return $this->respuesta;
    }

    public function setRespuesta(?string $respuesta): static
    {
        $this->respuesta = $respuesta;

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

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    #[ORM\PreUpdate]
    public function updateTimestamp(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
