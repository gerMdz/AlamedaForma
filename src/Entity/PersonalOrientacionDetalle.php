<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\PersonalOrientacionDetalleRepository;
use App\State\PersonalOrientacionDetalleProcessor;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: '/personal-orientacion-detalle'),
        new Get(uriTemplate: '/personal-orientacion-detalle/{id}'),
        new Post(uriTemplate: '/personal-orientacion-detalle', processor: PersonalOrientacionDetalleProcessor::class),
        new Patch(uriTemplate: '/personal-orientacion-detalle/{id}', processor: PersonalOrientacionDetalleProcessor::class),
        new Delete(uriTemplate: '/personal-orientacion-detalle/{id}')
    ],
    normalizationContext: ['groups' => ['pod:read']],
    denormalizationContext: ['groups' => ['pod:write']]
)]
#[ORM\Table(name: 'personal_orientacion_detalle')]
#[ORM\UniqueConstraint(name: 'uniq_po_detalle', columns: ['personal_orientacion_id','detalle_orientacion_id'])]
#[ORM\UniqueConstraint(name: 'uniq_po_posicion', columns: ['personal_orientacion_id','posicion'])]
#[ORM\Entity(repositoryClass: PersonalOrientacionDetalleRepository::class)]
class PersonalOrientacionDetalle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['pod:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: 'personal_orientacion_id', referencedColumnName: 'id')]
    #[Groups(['pod:read','pod:write'])]
    private ?PersonalOrientacion $personalOrientacion = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: 'detalle_orientacion_id', referencedColumnName: 'id')]
    #[Groups(['pod:read','pod:write'])]
    private ?DetalleOrientacion $detalleOrientacion = null;

    // Allows ordering 1..3 and enforces max three per personal_orientacion via unique constraint
    #[ORM\Column(type: 'smallint')]
    #[Assert\Range(min: 1, max: 3)]
    #[Groups(['pod:read','pod:write'])]
    private ?int $posicion = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['pod:read'])]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['pod:read'])]
    private ?DateTimeImmutable $updatedAt = null;

    public function getId(): ?int { return $this->id; }

    public function getPersonalOrientacion(): ?PersonalOrientacion { return $this->personalOrientacion; }
    public function setPersonalOrientacion(?PersonalOrientacion $po): static { $this->personalOrientacion = $po; return $this; }

    public function getDetalleOrientacion(): ?DetalleOrientacion { return $this->detalleOrientacion; }
    public function setDetalleOrientacion(?DetalleOrientacion $do): static { $this->detalleOrientacion = $do; return $this; }

    public function getPosicion(): ?int { return $this->posicion; }
    public function setPosicion(int $posicion): static { $this->posicion = $posicion; return $this; }

    public function getCreatedAt(): ?DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(?DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }
}
