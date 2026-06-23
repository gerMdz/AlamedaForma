<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\AntecedentesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: '/antecedentes'),
        new Get(uriTemplate: '/antecedentes/{id}')
    ],
    normalizationContext: ['groups' => ['antecedentes:read']]
)]
#[ORM\Entity(repositoryClass: AntecedentesRepository::class)]
class Antecedentes
{
    #[ORM\Id]
    #[ORM\Column(type: 'custom_uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['antecedentes:read'])]
    private ?Uuid $id = null;

    #[ORM\Column]
    #[Groups(['antecedentes:read'])]
    private ?bool $activo = null;

    #[ORM\Column(length: 255)]
    #[Groups(['antecedentes:read'])]
    private ?string $label = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['antecedentes:read'])]
    private ?string $consulta = null;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['antecedentes:read'])]
    private bool $esTitulo = false;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->activo = true;
        $this->esTitulo = false;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function isActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): static
    {
        $this->activo = $activo;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getConsulta(): ?string
    {
        return $this->consulta;
    }

    public function setConsulta(string $consulta): static
    {
        $this->consulta = $consulta;

        return $this;
    }

    public function isEsTitulo(): bool
    {
        return $this->esTitulo;
    }

    public function setEsTitulo(bool $esTitulo): static
    {
        $this->esTitulo = $esTitulo;

        return $this;
    }
}
