<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\HabilidadesRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: '/habilidades'),
        new Get(uriTemplate: '/habilidades/{id}')
    ],
    normalizationContext: ['groups' => ['habilidades:read']]
)]
#[ORM\Entity(repositoryClass: HabilidadesRepository::class)]
class Habilidades
{
    #[ORM\Id]
    #[ORM\Column(type: 'custom_uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['habilidades:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['habilidades:read'])]
    private ?string $nombre = null;

    // Nota: El requerimiento utiliza "discripcion" con 's'. Respetamos el nombre solicitado.
    #[ORM\Column(length: 255)]
    #[Groups(['habilidades:read'])]
    private ?string $discripcion = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $identificador = null;

    #[ORM\Column(name: 'deleted_at', nullable: true)]
    #[Groups(['habilidades:read'])]
    private ?DateTimeImmutable $deletedAt = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getDiscripcion(): ?string
    {
        return $this->discripcion;
    }

    public function setDiscripcion(string $discripcion): static
    {
        $this->discripcion = $discripcion;
        return $this;
    }

    public function getIdentificador(): ?string
    {
        return $this->identificador;
    }

    public function setIdentificador(string $identificador): static
    {
        $this->identificador = $identificador;
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

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }
}
