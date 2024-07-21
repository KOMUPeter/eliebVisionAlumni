<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Repository\ImagesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
#[Vich\Uploadable]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'images_files_products', fileNameProperty: 'fileName', size: 'size')]
    #[Assert\File(
        maxSize: '1M', // Limit file size to 500KB for profile pictures
        mimeTypes: ['image/jpeg', 'image/png', 'image/gif'],
        mimeTypesMessage: 'Please upload a valid image file (JPEG, PNG, GIF).'
    )]
    private ?File $file = null;

    #[ORM\Column(length: 255)]
    private ?string $fileName = null;

    #[ORM\Column]
    private ?int $size = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $uploadedAt = null;


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

    #[ORM\OneToOne(mappedBy: 'profileImage', cascade: ['persist', 'remove'])]
    private ?Users $usersProfileImage = null;


    public function __construct()
    {
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

    public function setFileName(?string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file = null): static
    {
        $this->file = $file;

        if (null !== $file) {
            $this->uploadedAt = new \DateTimeImmutable();
        }

        return $this;
    }
    // Example of using Doctrine lifecycle callbacks to ensure size is always set
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function ensureSizeIsSet(): void
    {
        if ($this->size === null && $this->file instanceof File) {
            $this->size = $this->file->getSize();
        }
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): static
    {
        $this->size = $size;

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

    public function __toString(): string
    {
        return sprintf(
            'Image #%d: %s (%d bytes)',
            $this->id,
            $this->fileName,
            $this->size
        );
    }

    public function getUsersProfileImage(): ?Users
    {
        return $this->usersProfileImage;
    }

    public function setUsersProfileImage(?Users $usersProfileImage): static
    {
        // unset the owning side of the relation if necessary
        if ($usersProfileImage === null && $this->usersProfileImage !== null) {
            $this->usersProfileImage->setProfileImage(null);
        }

        // set the owning side of the relation if necessary
        if ($usersProfileImage !== null && $usersProfileImage->getProfileImage() !== $this) {
            $usersProfileImage->setProfileImage($this);
        }

        $this->usersProfileImage = $usersProfileImage;

        return $this;
    }
}
