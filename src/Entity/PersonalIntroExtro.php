<?php

namespace App\Entity;

use App\Repository\PersonalIntroExtroRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PersonalIntroExtroRepository::class)]
class PersonalIntroExtro
{
    #[ORM\Id]
    #[ORM\Column(type: 'custom_uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: Personales::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Personales $person = null;

    // Snapshot de los datos de IntroExtro guardados en JSON
    #[ORM\Column(type: 'json')]
    private array $introExtroData = [];

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getPerson(): ?Personales
    {
        return $this->person;
    }

    public function setPerson(Personales $person): self
    {
        $this->person = $person;
        return $this;
    }

    /**
     * Devuelve el snapshot JSON con los datos de IntroExtro (por ejemplo: intro, extro, mitad, activo)
     */
    public function getIntroExtroData(): array
    {
        return $this->introExtroData;
    }

    /**
     * Establece el snapshot JSON con los datos de IntroExtro
     * Ejemplo de estructura esperada:
     * [
     *   'intro' => '...',
     *   'extro' => '...',
     *   'mitad' => '50/50',
     *   'activo' => true,
     *   'source_id' => 123, // opcional: id del registro en IntroExtro si aplica
     * ]
     */
    public function setIntroExtroData(array $introExtroData): self
    {
        $this->introExtroData = $introExtroData;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
