<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $fileName = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column]
    private ?int $size = null;

    /**
     * @var Collection<int, Users>
     */
    #[ORM\OneToMany(targetEntity: Users::class, mappedBy: 'profileImage')]
    private Collection $userProfileImage;

    /**
     * @var Collection<int, GroupMessage>
     */
    #[ORM\ManyToMany(targetEntity: GroupMessage::class, inversedBy: 'groupImages')]
    private Collection $groupImages;

    /**
     * @var Collection<int, PrivateMessage>
     */
    #[ORM\ManyToMany(targetEntity: PrivateMessage::class, inversedBy: 'privateImages')]
    private Collection $privateImages;

    #[ORM\Column]
    private ?\DateTimeImmutable $uploadedAt = null;

    public function __construct()
    {
        $this->userProfileImage = new ArrayCollection();
        $this->groupImages = new ArrayCollection();
        $this->privateImages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getUserProfileImage(): Collection
    {
        return $this->userProfileImage;
    }

    public function addUserProfileImage(Users $userProfileImage): static
    {
        if (!$this->userProfileImage->contains($userProfileImage)) {
            $this->userProfileImage->add($userProfileImage);
            $userProfileImage->setProfileImage($this);
        }

        return $this;
    }

    public function removeUserProfileImage(Users $userProfileImage): static
    {
        if ($this->userProfileImage->removeElement($userProfileImage)) {
            // set the owning side to null (unless already changed)
            if ($userProfileImage->getProfileImage() === $this) {
                $userProfileImage->setProfileImage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GroupMessage>
     */
    public function getGroupImages(): Collection
    {
        return $this->groupImages;
    }

    public function addGroupImage(GroupMessage $groupImage): static
    {
        if (!$this->groupImages->contains($groupImage)) {
            $this->groupImages->add($groupImage);
        }

        return $this;
    }

    public function removeGroupImage(GroupMessage $groupImage): static
    {
        $this->groupImages->removeElement($groupImage);

        return $this;
    }

    /**
     * @return Collection<int, PrivateMessage>
     */
    public function getPrivateImages(): Collection
    {
        return $this->privateImages;
    }

    public function addPrivateImage(PrivateMessage $privateImage): static
    {
        if (!$this->privateImages->contains($privateImage)) {
            $this->privateImages->add($privateImage);
        }

        return $this;
    }

    public function removePrivateImage(PrivateMessage $privateImage): static
    {
        $this->privateImages->removeElement($privateImage);

        return $this;
    }

    public function getUploadedAt(): ?\DateTimeImmutable
    {
        return $this->uploadedAt;
    }

    public function setUploadedAt(\DateTimeImmutable $uploadedAt): static
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }
}
