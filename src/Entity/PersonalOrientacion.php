<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\PersonalOrientacionRepository;
use App\State\PersonalOrientacionProcessor;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: '/personal-orientacion'),
        new Get(uriTemplate: '/personal-orientacion/{id}'),
        new Post(uriTemplate: '/personal-orientacion', processor: PersonalOrientacionProcessor::class),
        new Patch(uriTemplate: '/personal-orientacion/{id}', processor: PersonalOrientacionProcessor::class),
        new Delete(uriTemplate: '/personal-orientacion/{id}')
    ],
    normalizationContext: ['groups' => ['po:read']],
    denormalizationContext: ['groups' => ['po:write']]
)]
#[ORM\Table(name: 'personal_orientacion')]
#[ORM\Entity(repositoryClass: PersonalOrientacionRepository::class)]
class PersonalOrientacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['po:read'])]
    private ?int $id = null;

    // Optional inverse to access related Detalles (not required by feature, kept minimal)
    // #[ORM\OneToMany(mappedBy: 'personalOrientacion', targetEntity: PersonalOrientacionDetalle::class, orphanRemoval: true)]
    // private Collection $detalles;

    #[ORM\ManyToOne(inversedBy: null)]
    #[ORM\JoinColumn(nullable: false, name: 'persona_id', referencedColumnName: 'id')]
    #[Groups(['po:read','po:write'])]
    private ?Personales $persona = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['po:read','po:write'])]
    private ?string $action_1 = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['po:read','po:write'])]
    private ?string $action_2 = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['po:read','po:write'])]
    private ?string $action_3 = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['po:read','po:write'])]
    private ?string $trabajar = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['po:read','po:write'])]
    private ?string $resolver = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['po:read'])]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['po:read'])]
    private ?DateTimeImmutable $updatedAt = null;

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

    public function getAction1(): ?string
    {
        return $this->action_1;
    }

    public function setAction1(?string $action_1): static
    {
        $this->action_1 = $action_1;
        return $this;
    }

    public function getAction2(): ?string
    {
        return $this->action_2;
    }

    public function setAction2(?string $action_2): static
    {
        $this->action_2 = $action_2;
        return $this;
    }

    public function getAction3(): ?string
    {
        return $this->action_3;
    }

    public function setAction3(?string $action_3): static
    {
        $this->action_3 = $action_3;
        return $this;
    }

    public function getTrabajar(): ?string
    {
        return $this->trabajar;
    }

    public function setTrabajar(?string $trabajar): static
    {
        $this->trabajar = $trabajar;
        return $this;
    }

    public function getResolver(): ?string
    {
        return $this->resolver;
    }

    public function setResolver(?string $resolver): static
    {
        $this->resolver = $resolver;
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
