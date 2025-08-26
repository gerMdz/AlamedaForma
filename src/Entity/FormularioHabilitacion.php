<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\FormularioHabilitacionRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ApiResource]
#[ORM\Entity(repositoryClass: FormularioHabilitacionRepository::class)]
class FormularioHabilitacion
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    private ?string $id = null;

    // Identificador estable del formulario (ej.: "T" para Términos y condiciones)
    #[ORM\Column(length: 50, nullable: true, unique: true)]
    private ?string $identifier = null;

    // Nombre del formulario a habilitar
    #[ORM\Column(length: 255)]
    private ?string $nombreFormulario = null;

    // Fecha desde la cual el formulario estará activo
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $activoDesde = null;

    // Fecha hasta la cual el formulario estará activo (puede ser null para indefinido)
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $activoHasta = null;

    public function __construct()
    {
        // Generate UUID string if not set
        $this->id = Uuid::v4()->toRfc4122();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getNombreFormulario(): ?string
    {
        return $this->nombreFormulario;
    }

    public function setNombreFormulario(string $nombreFormulario): self
    {
        $this->nombreFormulario = $nombreFormulario;
        return $this;
    }

    public function getActivoDesde(): ?DateTimeImmutable
    {
        return $this->activoDesde;
    }

    public function setActivoDesde(DateTimeImmutable $activoDesde): self
    {
        $this->activoDesde = $activoDesde;
        return $this;
    }

    public function getActivoHasta(): ?DateTimeImmutable
    {
        return $this->activoHasta;
    }

    public function setActivoHasta(?DateTimeImmutable $activoHasta): self
    {
        $this->activoHasta = $activoHasta;
        return $this;
    }

    // Helper: indica si el formulario está activo en la fecha/hora dada
    public function isActivo(?DateTimeImmutable $at = null): bool
    {
        $at = $at ?? new DateTimeImmutable();
        if ($this->activoDesde && $at < $this->activoDesde) {
            return false;
        }
        if ($this->activoHasta && $at > $this->activoHasta) {
            return false;
        }
        return true;
    }
}
