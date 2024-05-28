<?php

namespace App\Entity;

use App\Repository\NextOfKinRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NextOfKinRepository::class)]
class NextOfKin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nextOfKinFirstName = null;

    #[ORM\Column(length: 255)]
    private ?string $nextOfKinLastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nextOfKinEmail = null;

    #[ORM\Column(nullable: true)]
    private ?int $nextOfKinPhone = null;

    #[ORM\ManyToOne(inversedBy: 'nextOfKin')]
    private ?Users $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNextOfKinFirstName(): ?string
    {
        return $this->nextOfKinFirstName;
    }

    public function setNextOfKinFirstName(string $nextOfKinFirstName): static
    {
        $this->nextOfKinFirstName = $nextOfKinFirstName;

        return $this;
    }

    public function getNextOfKinLastName(): ?string
    {
        return $this->nextOfKinLastName;
    }

    public function setNextOfKinLastName(string $nextOfKinLastName): static
    {
        $this->nextOfKinLastName = $nextOfKinLastName;

        return $this;
    }

    public function getNextOfKinEmail(): ?string
    {
        return $this->nextOfKinEmail;
    }

    public function setNextOfKinEmail(?string $nextOfKinEmail): static
    {
        $this->nextOfKinEmail = $nextOfKinEmail;

        return $this;
    }

    public function getNextOfKinPhone(): ?int
    {
        return $this->nextOfKinPhone;
    }

    public function setNextOfKinPhone(?int $nextOfKinPhone): static
    {
        $this->nextOfKinPhone = $nextOfKinPhone;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): static
    {
        $this->user = $user;

        return $this;
    }
}
