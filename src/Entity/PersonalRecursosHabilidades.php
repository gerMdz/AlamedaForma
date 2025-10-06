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
use App\Repository\PersonalRecursosHabilidadesRepository;
use App\State\PersonalRecursosHabilidadesProcessor;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: '/personal-recursos-habilidades'),
        new Get(uriTemplate: '/personal-recursos-habilidades/{id}'),
        new Post(uriTemplate: '/personal-recursos-habilidades', processor: PersonalRecursosHabilidadesProcessor::class),
        new Patch(uriTemplate: '/personal-recursos-habilidades/{id}', processor: PersonalRecursosHabilidadesProcessor::class),
        new Delete(uriTemplate: '/personal-recursos-habilidades/{id}')
    ],
    normalizationContext: ['groups' => ['prh:read']],
    denormalizationContext: ['groups' => ['prh:write']]
)]
#[ApiFilter(SearchFilter::class, properties: ['personalRecursos' => 'exact'])]
#[ORM\Table(name: 'personal_recursos_habilidades')]
#[ORM\UniqueConstraint(name: 'uniq_pr_habilidad', columns: ['personal_recursos_id','habilidad_id'])]
#[ORM\Entity(repositoryClass: PersonalRecursosHabilidadesRepository::class)]
class PersonalRecursosHabilidades
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['prh:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: 'personal_recursos_id', referencedColumnName: 'id')]
    #[Groups(['prh:read','prh:write'])]
    private ?PersonalRecursos $personalRecursos = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: 'habilidad_id', referencedColumnName: 'id')]
    #[Groups(['prh:read','prh:write'])]
    private ?Habilidades $habilidad = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['prh:read'])]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['prh:read'])]
    private ?DateTimeImmutable $updatedAt = null;

    public function getId(): ?int { return $this->id; }

    public function getPersonalRecursos(): ?PersonalRecursos { return $this->personalRecursos; }
    public function setPersonalRecursos(?PersonalRecursos $pr): static { $this->personalRecursos = $pr; return $this; }

    public function getHabilidad(): ?Habilidades { return $this->habilidad; }
    public function setHabilidad(?Habilidades $hab): static { $this->habilidad = $hab; return $this; }

    public function getCreatedAt(): ?DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(?DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }
}
