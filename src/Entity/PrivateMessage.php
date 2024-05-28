<?php

namespace App\Entity;

use App\Repository\PrivateMessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrivateMessageRepository::class)]
class PrivateMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $sentAt = null;

    #[ORM\ManyToOne(inversedBy: 'privateMessages')]
    private ?Users $sender = null;

    #[ORM\ManyToOne(inversedBy: 'recipientPrivateMessage')]
    private ?Users $recipient = null;

    /**
     * @var Collection<int, Images>
     */
    #[ORM\ManyToMany(targetEntity: Images::class, mappedBy: 'privateImages')]
    private Collection $privateImages;

    public function __construct()
    {
        $this->privateImages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeImmutable $sentAt): static
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getSender(): ?Users
    {
        return $this->sender;
    }

    public function setSender(?Users $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecipient(): ?Users
    {
        return $this->recipient;
    }

    public function setRecipient(?Users $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getPrivateImages(): Collection
    {
        return $this->privateImages;
    }

    public function addPrivateImage(Images $privateImage): static
    {
        if (!$this->privateImages->contains($privateImage)) {
            $this->privateImages->add($privateImage);
            $privateImage->addPrivateImage($this);
        }

        return $this;
    }

    public function removePrivateImage(Images $privateImage): static
    {
        if ($this->privateImages->removeElement($privateImage)) {
            $privateImage->removePrivateImage($this);
        }

        return $this;
    }
}
