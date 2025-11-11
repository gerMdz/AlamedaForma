<?php

namespace App\Entity;

use App\Repository\PersonalDiscRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PersonalDiscRepository::class)]
class PersonalDisc
{
    #[ORM\Id]
    #[ORM\Column(type: 'custom_uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: Personales::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Personales $person = null;

    // Puntuaciones DISC (enteros)
    #[ORM\Column(type: 'integer')]
    private int $d = 0;

    #[ORM\Column(type: 'integer')]
    private int $i = 0;

    #[ORM\Column(type: 'integer')]
    private int $s = 0;

    #[ORM\Column(type: 'integer')]
    private int $c = 0;


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

    public function getD(): int
    {
        return $this->d;
    }

    public function setD(int $d): self
    {
        $this->d = $d;
        return $this;
    }

    public function getI(): int
    {
        return $this->i;
    }

    public function setI(int $i): self
    {
        $this->i = $i;
        return $this;
    }

    public function getS(): int
    {
        return $this->s;
    }

    public function setS(int $s): self
    {
        $this->s = $s;
        return $this;
    }

    public function getC(): int
    {
        return $this->c;
    }

    public function setC(int $c): self
    {
        $this->c = $c;
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
