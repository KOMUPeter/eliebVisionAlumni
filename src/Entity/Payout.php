<?php

namespace App\Entity;

use App\Repository\PayoutRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PayoutRepository::class)]
class Payout
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $paydate = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\ManyToOne(inversedBy: 'payouts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $month = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaydate(): ?\DateTimeInterface
    {
        return $this->paydate;
    }

    public function setPaydate(\DateTimeInterface $paydate): static
    {
        $this->paydate = $paydate;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

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
    public function __toString(): string
    {
        return sprintf('Payout #%d: %d on %s', $this->id, 
        $this->amount, 
        $this->paydate->format('Y-m-d'));
    }

    public function getMonth(): ?\DateTimeInterface
    {
        return $this->month;
    }

    public function setMonth(?\DateTimeInterface $month): static
    {
        $this->month = $month;

        return $this;
    }

}
