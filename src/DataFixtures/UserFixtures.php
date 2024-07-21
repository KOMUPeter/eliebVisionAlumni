<?php

namespace App\DataFixtures;

use App\Entity\Users;
use App\Entity\Subscription;
use App\Entity\NextOfKin;
use App\Entity\Images;
use App\Entity\Payout;
use App\Entity\PrivateMessage;
use App\Entity\GroupMessage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        // Create a user
        $user = new Users();
        $user->setEmail('admin@example.com');
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'adminpass'));
        $user->setFirstName('John');
        $user->setLastName('Admin');
        $user->setPhoneNumber('123456789');
        $user->setIsSubscribed(true);
        $user->setNextOfKins('Jane Doe');
        $user->setNextOfKinTel('987654321');
        $user->setRegistrationAmount(100);
        $user->setUpdatedAt(new \DateTimeImmutable());

        // Create and associate a profile image
        $profileImage = new Images();
        $profileImage->setFileName('peter.jpg'); 

        $filePath = __DIR__ . '/../../public/images/peter.jpg';

        $profileImage->setUploadedAt(new \DateTimeImmutable());
        
        $size = filesize($filePath);
        $profileImage->setSize($size);
        $user->setProfileImage($profileImage);

        
        $manager->persist($user);
        $manager->persist($profileImage);

        // Flush all the persisted objects
        $manager->flush();
    }
}

