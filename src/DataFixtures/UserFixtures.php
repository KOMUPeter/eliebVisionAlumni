<?php

namespace App\DataFixtures;

use App\Entity\Users;
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

    public function load(ObjectManager $manager): void
    {
        // Define some example users
        $users = [
            [
                'email' => 'user@example.com',
                'password' => 'password123',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'phoneNumber' => '1234567890',
                'registrationDate' => new \DateTime(),
                'isSubscribed' => true,
                'roles' => ['ROLE_USER', 'ROLE_MEMBER'],
                'nextOfKin' => [
                    'nextOfKinFirstName' => 'Jane',
                    'nextOfKinLastName' => 'Doe',
                    'nextOfKinEmail' => 'jane.doe@example.com',
                    'nextOfKinPhone' => '987654321',
                ],
                'profileImage' => [
                    'fileName' => 'profile_image1.jpg',
                    'path' => 'path/to/profile/image1.jpg',
                    'size' => 2048,
                    'uploadedAt' => new \DateTimeImmutable(),
                ],
                'payouts' => [
                    ['amount' => 100, 'paydate' => new \DateTime()],
                ],
                'privateMessages' => [
                    ['content' => 'Hello', 'recipientEmail' => 'admin@example.com', 'sentAt' => new \DateTimeImmutable()],
                ],
                'recipientPrivateMessages' => []
            ],
            [
                'email' => 'admin@example.com',
                'password' => 'adminpass',
                'firstName' => 'Jane',
                'lastName' => 'Smith',
                'phoneNumber' => '987654321',
                'registrationDate' => new \DateTime(),
                'isSubscribed' => false,
                'roles' => ['ROLE_ADMIN', 'ROLE_SECRETARY'],
                'nextOfKin' => [
                    'nextOfKinFirstName' => 'John',
                    'nextOfKinLastName' => 'Smith',
                    'nextOfKinEmail' => 'john.smith@example.com',
                    'nextOfKinPhone' => '123456789',
                ],
                'profileImage' => [
                    'fileName' => 'profile_image2.jpg',
                    'path' => 'path/to/profile/image2.jpg',
                    'size' => 2048,
                    'uploadedAt' => new \DateTimeImmutable(),
                ],
                'payouts' => [
                    ['amount' => 200, 'paydate' => new \DateTime()],
                ],
                'privateMessages' => [],
                'recipientPrivateMessages' => [
                    ['content' => 'Hello back', 'senderEmail' => 'user@example.com', 'sentAt' => new \DateTimeImmutable()],
                ]
            ]
        ];

        foreach ($users as $userData) {
            $user = new Users();
            $user->setEmail($userData['email']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $userData['password']));
            $user->setFirstName($userData['firstName']);
            $user->setLastName($userData['lastName']);
            $user->setPhoneNumber($userData['phoneNumber']);
            $user->setRegistrationDate($userData['registrationDate']);
            $user->setIsSubscribed($userData['isSubscribed']);
            $user->setRoles($userData['roles']);

            // Set next of kin
            $nextOfKinData = $userData['nextOfKin'];
            $nextOfKin = new NextOfKin();
            $nextOfKin->setNextOfKinFirstName($nextOfKinData['nextOfKinFirstName']);
            $nextOfKin->setNextOfKinLastName($nextOfKinData['nextOfKinLastName']);
            $nextOfKin->setNextOfKinEmail($nextOfKinData['nextOfKinEmail']);
            $nextOfKin->setNextOfKinPhone($nextOfKinData['nextOfKinPhone']);
            $user->addNextOfKin($nextOfKin);
            $manager->persist($nextOfKin);

            // Set profile image
            $profileImageData = $userData['profileImage'];
            $profileImage = new Images();
            $profileImage->setFileName($profileImageData['fileName']);
            $profileImage->setPath($profileImageData['path']);
            $profileImage->setSize($profileImageData['size']);
            $profileImage->setUploadedAt($profileImageData['uploadedAt']);
            $user->setProfileImage($profileImage);
            $manager->persist($profileImage);

            // Add payouts
            foreach ($userData['payouts'] as $payoutData) {
                $payout = new Payout();
                $payout->setAmount($payoutData['amount']);
                $payout->setPaydate($payoutData['paydate']);
                $user->addPayout($payout);
                $manager->persist($payout);
            }

            // Add private messages (sent by user)
            foreach ($userData['privateMessages'] as $messageData) {
                $message = new PrivateMessage();
                $message->setContent($messageData['content']);
                $message->setSender($user);
                $recipient = $manager->getRepository(Users::class)->findOneBy(['email' => $messageData['recipientEmail']]);
                $message->setRecipient($recipient);
                $message->setSentAt($messageData['sentAt']);
                $user->addPrivateMessage($message);
                $manager->persist($message);
            }

            // Add private messages (received by user)
            foreach ($userData['recipientPrivateMessages'] as $messageData) {
                $message = new PrivateMessage();
                $message->setContent($messageData['content']);
                $message->setRecipient($user);
                $sender = $manager->getRepository(Users::class)->findOneBy(['email' => $messageData['senderEmail']]);
                $message->setSender($sender);
                $message->setSentAt($messageData['sentAt']);
                $user->addRecipientPrivateMessage($message);
                $manager->persist($message);
            }

            $manager->persist($user);
        }

        $manager->flush();
    }
}
