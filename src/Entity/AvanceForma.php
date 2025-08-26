<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AvanceFormaRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ApiResource]
#[ORM\Entity(repositoryClass: AvanceFormaRepository::class)]
class AvanceForma
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    private ?string $id = null;

    // Relación con la persona (Personales)
    #[ORM\ManyToOne(targetEntity: Personales::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personales $persona = null;

    // Relación con el formulario habilitado (FormularioHabilitacion)
    #[ORM\ManyToOne(targetEntity: FormularioHabilitacion::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?FormularioHabilitacion $formulario = null;

    // Fecha en la que la persona completa la etapa/formulario
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $fechaEtapa = null;

    public function __construct()
    {
        // Generate UUID string if not set
        $this->id = Uuid::v4()->toRfc4122();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPersona(): ?Personales
    {
        return $this->persona;
    }

    public function setPersona(Personales $persona): self
    {
        $this->persona = $persona;
        return $this;
    }

    public function getFormulario(): ?FormularioHabilitacion
    {
        return $this->formulario;
    }

    public function setFormulario(FormularioHabilitacion $formulario): self
    {
        $this->formulario = $formulario;
        return $this;
    }

    public function getFechaEtapa(): ?DateTimeImmutable
    {
        return $this->fechaEtapa;
    }

    public function setFechaEtapa(DateTimeImmutable $fechaEtapa): self
    {
        $this->fechaEtapa = $fechaEtapa;
        return $this;
    }
}
