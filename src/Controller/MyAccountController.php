<?php
// src/Controller/MyAccountController.php
namespace App\Controller;

use App\Entity\Payout;
use App\Entity\Users;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormError;

class MyAccountController extends AbstractController
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
    }

    #[Route('/myAccount', name: 'myAccount')]
    public function changeUserPassword(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to access this page.');
        }

        // Retrieve all payouts for the logged-in user
        $payouts = $this->entityManager->getRepository(Payout::class)->findBy(['user' => $user], ['month' => 'ASC']);
        $totalPaid = array_sum(array_map(fn($payout) => $payout->getAmount(), $payouts));

        // Calculate total amount due
        $subscriptionStartDate = $user->getUpdatedAt();
        $currentDate = new \DateTimeImmutable();
        $monthsSubscribed = $subscriptionStartDate->diff($currentDate)->y * 12 + $subscriptionStartDate->diff($currentDate)->m + 1;

        $totalDue = 0;
        for ($i = 0; $i < $monthsSubscribed; $i++) {
            $month = $subscriptionStartDate->modify("+$i months");
            $totalDue += ($month >= new \DateTimeImmutable('2024-01-01')) ? 500 : 300;
        }

        // Determine if the user is overpaid or has pending dues
        $amountOverpaid = $totalPaid - $totalDue;
        $pendingDues = $totalDue - $totalPaid;

        $nextPaymentDate = null;
        $remainder = 0;
        $message = '';
        $messageType = '';

        if ($amountOverpaid >= 0) {
            // Calculate the next payment date for overpaid amount
            $remainingBalance = $amountOverpaid;
            $nextPaymentDate = clone $subscriptionStartDate;
            $monthlyRate = $subscriptionStartDate >= new \DateTimeImmutable('2024-01-01') ? 500 : 300;

            while ($remainingBalance >= $monthlyRate) {
                $nextPaymentDate = $nextPaymentDate->modify("+1 month");
                $remainingBalance -= $monthlyRate;
            }

            // Set remainder
            $remainder = $remainingBalance;
            $message = 'Thanks for keeping up with the group!';
            $messageType = 'positive';
        } else {
            // Calculate the next payment date for pending dues
            $remainingBalance = $pendingDues;
            $nextPaymentDate = clone $subscriptionStartDate;
            $monthlyRate = $subscriptionStartDate >= new \DateTimeImmutable('2024-01-01') ? 500 : 300;

            while ($remainingBalance > 0) {
                $nextPaymentDate = $nextPaymentDate->modify("+1 month");
                $remainingBalance -= $monthlyRate;
            }

            // Set remainder
            $remainder = abs($remainingBalance);
            $message = "Please settle your outstanding balance, which was due before " . $nextPaymentDate->format('Y-m-d');
            $messageType = 'negative';
        }

        // Create and handle form
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if (!$this->userPasswordHasher->isPasswordValid($user, $data['currentPassword'])) {
                $form->get('currentPassword')->addError(new FormError('Current password is incorrect.'));
            } else {
                $newPassword = $this->userPasswordHasher->hashPassword($user, $data['newPassword']);
                $user->setPassword($newPassword);
                $this->entityManager->flush();
                $this->addFlash('success', 'Password changed successfully.');
                return $this->redirectToRoute('app_home_page');
            }
        }

        return $this->render('my_account/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'totalPaid' => $totalPaid,
            'totalDue' => $totalDue,
            'paymentStatus' => $totalPaid >= $totalDue 
                ? 'Paid up-to-date' 
                : 'You have Pending',
            'amountOverpaid' => $amountOverpaid,
            'nextPaymentDate' => $nextPaymentDate,
            'remainder' => $remainder,
            'pendingDues' => $pendingDues,
            'message' => $message,
            'messageType' => $messageType, // 'positive' or 'negative'
        ]);
        
    }
}
