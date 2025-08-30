<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\PersonalFormationRepository;
use App\State\PersonalFormationStateProvider;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/personal-formation',
        )
    ],
    provider: PersonalFormationStateProvider::class
)]
#[ORM\Entity(repositoryClass: PersonalFormationRepository::class)]
class PersonalFormation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $percentDon = null;

    #[ORM\Column(length: 200)]
    private ?string $commentDon = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dones $don = null;

    #[ORM\ManyToOne(inversedBy: 'personalFormations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personales $person = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPercentDon(): ?int
    {
        return $this->percentDon;
    }

    public function setPercentDon(int $percentDon): static
    {
        $this->percentDon = $percentDon;

        return $this;
    }

    public function getCommentDon(): ?string
    {
        return $this->commentDon;
    }

    public function setCommentDon(string $commentDon): static
    {
        $this->commentDon = $commentDon;

        return $this;
    }

    public function getDon(): ?Dones
    {
        return $this->don;
    }

    public function setDon(?Dones $don): static
    {
        $this->don = $don;

        return $this;
    }

    public function getPerson(): ?Personales
    {
        return $this->person;
    }

    public function setPerson(?Personales $person): static
    {
        $this->person = $person;

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
