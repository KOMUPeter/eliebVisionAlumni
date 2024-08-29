<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[Vich\Uploadable]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    private ?string $plainPassword = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column]
    private ?string $phoneNumber = null;

    #[ORM\Column]
    private ?bool $isSubscribed = null;

    #[ORM\Column(length: 255)]
    private ?string $nextOfKins = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nextOfKinTel = null;
    
    /**
     * @var Collection<int, Payout>
     */
    #[ORM\OneToMany(targetEntity: Payout::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $payouts;

    /**
     * @var Collection<int, PrivateMessage>
     */
    #[ORM\OneToMany(targetEntity: PrivateMessage::class, mappedBy: 'sender')]
    private Collection $privateMessages;

    /**
     * @var Collection<int, PrivateMessage>
     */
    #[ORM\OneToMany(targetEntity: PrivateMessage::class, mappedBy: 'recipient')]
    private Collection $recipientPrivateMessage;

    /**
     * @var Collection<int, GroupMessage>
     */
    #[ORM\OneToMany(targetEntity: GroupMessage::class, mappedBy: 'groupSender')]
    private Collection $groupMessages;

    /**
     * @var Collection<int, GroupMessage>
     */
    #[ORM\ManyToMany(targetEntity: GroupMessage::class, mappedBy: 'groupRecipient')]
    private Collection $groupRecipient;

    #[ORM\Column]
    private ?int $registrationAmount = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToOne(inversedBy: 'usersProfileImage', cascade: ['persist', 'remove'])]
    private ?Images $profileImage = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deactivationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $reactivationDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $outstandingAmount = null;

    
    public function __construct()
    {
        $this->payouts = new ArrayCollection();
        $this->privateMessages = new ArrayCollection();
        $this->recipientPrivateMessage = new ArrayCollection();
        $this->groupMessages = new ArrayCollection();
        $this->groupRecipient = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
        
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }
    
    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
    
        return $this;
    }
    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function isSubscribed(): ?bool
    {
        return $this->isSubscribed;
    }

    public function setIsSubscribed(bool $isSubscribed): static
    {
        // Check if the subscription status has changed from subscribed to not subscribed
        if (!$isSubscribed && $this->isSubscribed) {
            $this->deactivationDate = new \DateTime(); // Set the current date and time
        }
    
        // Check if the subscription status has changed from not subscribed to subscribed
        if ($isSubscribed && !$this->isSubscribed) {
            $this->reactivationDate = new \DateTime(); // Set the current date and time
        }
    
        // Update the subscription status
        $this->isSubscribed = $isSubscribed;
    
        // Return the current instance to allow method chaining
        return $this;
    }


    /**
     * @return Collection<int, Payout>
     */
    public function getPayouts(): Collection
    {
        return $this->payouts;
    }

    public function addPayout(Payout $payout): static
    {
        if (!$this->payouts->contains($payout)) {
            $this->payouts->add($payout);
            $payout->setUser($this);
        }

        return $this;
    }

    public function removePayout(Payout $payout): static
    {
        if ($this->payouts->removeElement($payout)) {
            // set the owning side to null (unless already changed)
            if ($payout->getUser() === $this) {
                $payout->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PrivateMessage>
     */
    public function getPrivateMessages(): Collection
    {
        return $this->privateMessages;
    }

    public function addPrivateMessage(PrivateMessage $privateMessage): static
    {
        if (!$this->privateMessages->contains($privateMessage)) {
            $this->privateMessages->add($privateMessage);
            $privateMessage->setSender($this);
        }

        return $this;
    }

    public function removePrivateMessage(PrivateMessage $privateMessage): static
    {
        if ($this->privateMessages->removeElement($privateMessage)) {
            // set the owning side to null (unless already changed)
            if ($privateMessage->getSender() === $this) {
                $privateMessage->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PrivateMessage>
     */
    public function getRecipientPrivateMessage(): Collection
    {
        return $this->recipientPrivateMessage;
    }

    public function addRecipientPrivateMessage(PrivateMessage $recipientPrivateMessage): static
    {
        if (!$this->recipientPrivateMessage->contains($recipientPrivateMessage)) {
            $this->recipientPrivateMessage->add($recipientPrivateMessage);
            $recipientPrivateMessage->setRecipient($this);
        }

        return $this;
    }

    public function removeRecipientPrivateMessage(PrivateMessage $recipientPrivateMessage): static
    {
        if ($this->recipientPrivateMessage->removeElement($recipientPrivateMessage)) {
            // set the owning side to null (unless already changed)
            if ($recipientPrivateMessage->getRecipient() === $this) {
                $recipientPrivateMessage->setRecipient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GroupMessage>
     */
    public function getGroupMessages(): Collection
    {
        return $this->groupMessages;
    }

    public function addGroupMessage(GroupMessage $groupMessage): static
    {
        if (!$this->groupMessages->contains($groupMessage)) {
            $this->groupMessages->add($groupMessage);
            $groupMessage->setGroupSender($this);
        }

        return $this;
    }

    public function removeGroupMessage(GroupMessage $groupMessage): static
    {
        if ($this->groupMessages->removeElement($groupMessage)) {
            // set the owning side to null (unless already changed)
            if ($groupMessage->getGroupSender() === $this) {
                $groupMessage->setGroupSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GroupMessage>
     */
    public function getGroupRecipient(): Collection
    {
        return $this->groupRecipient;
    }

    public function addGroupRecipient(GroupMessage $groupRecipient): static
    {
        if (!$this->groupRecipient->contains($groupRecipient)) {
            $this->groupRecipient->add($groupRecipient);
            $groupRecipient->addGroupRecipient($this);
        }

        return $this;
    }

    public function removeGroupRecipient(GroupMessage $groupRecipient): static
    {
        if ($this->groupRecipient->removeElement($groupRecipient)) {
            $groupRecipient->removeGroupRecipient($this);
        }

        return $this;
    }
    
    public function getRegistrationAmount(): ?int
    {
        return $this->registrationAmount;
    }

    public function setRegistrationAmount(int $registrationAmount): static
    {
        $this->registrationAmount = $registrationAmount;

        return $this;
    }

    public function getNextOfKinTel(): ?string
    {
        return $this->nextOfKinTel;
    }

    public function setNextOfKinTel(?string $nextOfKinTel): static
    {
        $this->nextOfKinTel = $nextOfKinTel;

        return $this;
    }
    /**
     * Get the value of nextOfKins
     */
    public function getNextOfKins(): ?string
    {
        return $this->nextOfKins;
    }

    /**
     * Set the value of nextOfKins
     *
     * @return self
     */
    public function setNextOfKins(?string $nextOfKins): self
    {
        $this->nextOfKins = $nextOfKins;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getProfileImage(): ?Images
    {
        return $this->profileImage;
    }

    public function setProfileImage(?Images $profileImage): static
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    public function __toString(): string
    {
        return $this->lastName . ' ' . $this->email;
    }

    public function getDeactivationDate(): ?\DateTimeInterface
    {
        return $this->deactivationDate;
    }

    public function setDeactivationDate(?\DateTimeInterface $deactivationDate): static
    {
        $this->deactivationDate = $deactivationDate;

        return $this;
    }

    public function getReactivationDate(): ?\DateTimeInterface
    {
        return $this->reactivationDate;
    }

    public function setReactivationDate(?\DateTimeInterface $reactivationDate): static
    {
        $this->reactivationDate = $reactivationDate;

        return $this;
    }

    public function getOutstandingAmount(): ?int
    {
        return $this->outstandingAmount;
    }

    public function setOutstandingAmount(?int $outstandingAmount): static
    {
        $this->outstandingAmount = $outstandingAmount;

        return $this;
    }

}
