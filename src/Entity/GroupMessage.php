<?php

namespace App\Entity;

use App\Repository\GroupMessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupMessageRepository::class)]
class GroupMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $groupContent = null;

    #[ORM\ManyToOne(inversedBy: 'groupMessages')]
    private ?Users $groupSender = null;

    /**
     * @var Collection<int, Users>
     */
    #[ORM\ManyToMany(targetEntity: Users::class, inversedBy: 'groupRecipient')]
    private Collection $groupRecipient;

    /**
     * @var Collection<int, Images>
     */
    #[ORM\ManyToMany(targetEntity: Images::class, mappedBy: 'groupImages')]
    private Collection $groupImages;

    public function __construct()
    {
        $this->groupRecipient = new ArrayCollection();
        $this->groupImages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupContent(): ?string
    {
        return $this->groupContent;
    }

    public function setGroupContent(string $groupContent): static
    {
        $this->groupContent = $groupContent;

        return $this;
    }

    public function getGroupSender(): ?Users
    {
        return $this->groupSender;
    }

    public function setGroupSender(?Users $groupSender): static
    {
        $this->groupSender = $groupSender;

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getGroupRecipient(): Collection
    {
        return $this->groupRecipient;
    }

    public function addGroupRecipient(Users $groupRecipient): static
    {
        if (!$this->groupRecipient->contains($groupRecipient)) {
            $this->groupRecipient->add($groupRecipient);
        }

        return $this;
    }

    public function removeGroupRecipient(Users $groupRecipient): static
    {
        $this->groupRecipient->removeElement($groupRecipient);

        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getGroupImages(): Collection
    {
        return $this->groupImages;
    }

    public function addGroupImage(Images $groupImage): static
    {
        if (!$this->groupImages->contains($groupImage)) {
            $this->groupImages->add($groupImage);
            $groupImage->addGroupImage($this);
        }

        return $this;
    }

    public function removeGroupImage(Images $groupImage): static
    {
        if ($this->groupImages->removeElement($groupImage)) {
            $groupImage->removeGroupImage($this);
        }

        return $this;
    }
}
