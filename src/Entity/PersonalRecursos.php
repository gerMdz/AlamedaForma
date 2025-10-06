<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\PersonalRecursosRepository;
use App\State\PersonalRecursosProcessor;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: '/personal-recursos'),
        new Get(uriTemplate: '/personal-recursos/{id}'),
        new Post(uriTemplate: '/personal-recursos', processor: PersonalRecursosProcessor::class),
        new Patch(uriTemplate: '/personal-recursos/{id}', processor: PersonalRecursosProcessor::class),
        new Delete(uriTemplate: '/personal-recursos/{id}')
    ],
    normalizationContext: ['groups' => ['pr:read']],
    denormalizationContext: ['groups' => ['pr:write']]
)]
#[ApiFilter(SearchFilter::class, properties: ['persona' => 'exact'])]
#[ORM\Table(name: 'personal_recursos', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uniq_personal_recursos_persona', columns: ['persona_id'])])]
#[ORM\Entity(repositoryClass: PersonalRecursosRepository::class)]
#[UniqueEntity(fields: ['persona'], message: 'Ya existe un registro de Recursos para esta persona.')]
class PersonalRecursos
{
    #[ORM\Id]
    #[ORM\Column(type: 'custom_uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['pr:read'])]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: null)]
    #[ORM\JoinColumn(nullable: false, name: 'persona_id', referencedColumnName: 'id')]
    #[Groups(['pr:read','pr:write'])]
    private ?Personales $persona = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['pr:read','pr:write'])]
    private ?string $vocacion = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['pr:read','pr:write'])]
    private ?string $trabajos = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['pr:read','pr:write'])]
    private ?string $clases = null;

    // "contribución" (sin acento en el nombre del campo por convención)
    #[ORM\Column(type: 'text', nullable: true, name: 'contribucion')]
    #[Groups(['pr:read','pr:write'])]
    private ?string $contribucion = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['pr:read'])]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['pr:read'])]
    private ?DateTimeImmutable $updatedAt = null;

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

    public function getVocacion(): ?string
    {
        return $this->vocacion;
    }

    public function setVocacion(?string $vocacion): static
    {
        $this->vocacion = $vocacion;
        return $this;
    }

    public function getTrabajos(): ?string
    {
        return $this->trabajos;
    }

    public function setTrabajos(?string $trabajos): static
    {
        $this->trabajos = $trabajos;
        return $this;
    }

    public function getClases(): ?string
    {
        return $this->clases;
    }

    public function setClases(?string $clases): static
    {
        $this->clases = $clases;
        return $this;
    }

    public function getContribucion(): ?string
    {
        return $this->contribucion;
    }

    public function setContribucion(?string $contribucion): static
    {
        $this->contribucion = $contribucion;
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
}
