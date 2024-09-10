<?php

namespace App\Controller;

use App\Entity\Payout;
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

    // Retrieve all payouts for the logged-in user (your existing logic)
    $payouts = $this->entityManager->getRepository(Payout::class)->findBy(['user' => $user], ['month' => 'ASC']);
    $totalPaid = array_sum(array_map(fn($payout) => $payout->getAmount(), $payouts));

    $subscriptionStartDate = $user->getUpdatedAt();
    $currentDate = new \DateTimeImmutable();
    $monthsSinceUpdate = $subscriptionStartDate->diff($currentDate)->y * 12 + $subscriptionStartDate->diff($currentDate)->m;
    if ($currentDate > $subscriptionStartDate) {
        $monthsSinceUpdate++;
    }

    $totalDue = 0;
    $currentDateIter = clone $subscriptionStartDate;

    for ($i = 0; $i < $monthsSinceUpdate; $i++) {
        $monthlyRate = $currentDateIter >= new \DateTimeImmutable('2023-01-01') ? 500 : 300;
        $totalDue += $monthlyRate;
        $currentDateIter = $currentDateIter->modify("+1 month");
    }

    $monthsCovered = 0;
    $totalPaidRemaining = $totalPaid;
    $currentDateIter = clone $subscriptionStartDate;
    while ($totalPaidRemaining > 0) {
        $monthlyRate = $currentDateIter >= new \DateTimeImmutable('2023-01-01') ? 500 : 300;
        if ($totalPaidRemaining >= $monthlyRate) {
            $monthsCovered++;
            $totalPaidRemaining -= $monthlyRate;
        } else {
            break;
        }
        $currentDateIter = $currentDateIter->modify("+1 month");
    }

    $pendingDues = $totalDue - $totalPaid;
    $unpaidMonths = $pendingDues > 0 ? $monthsSinceUpdate - $monthsCovered : 0;

    if ($pendingDues < 0) {
        $status = 'Over-paid';
        $amount = '+' . number_format(abs($pendingDues));
        $unpaidMonths = 0;
    } elseif ($pendingDues > 0) {
        $status = 'Pending Dues';
        $amount = '-' . number_format($pendingDues);
    } else {
        $status = 'All paid';
        $amount = '00';
    }

    $coverageDate = null;
    if ($pendingDues < 0) {
        $coverageDate = clone $subscriptionStartDate;
        $totalPaidRemaining = abs($pendingDues);
        while ($totalPaidRemaining > 0) {
            $monthlyRate = $coverageDate >= new \DateTimeImmutable('2023-01-01') ? 500 : 300;
            if ($totalPaidRemaining >= $monthlyRate) {
                $totalPaidRemaining -= $monthlyRate;
                $coverageDate = $coverageDate->modify("+1 month");
            } else {
                break;
            }
        }
        $coverageDate = $coverageDate->modify('last day of this month');
    }

    $nextPaymentDate = $pendingDues > 0 ? $currentDateIter->format('Y-m-d') : ($coverageDate ? $coverageDate->format('Y-m-d') : 'Any time from Today');
    $nextPaymentDateMessage = $pendingDues > 0 
        ? 'You should have cleared your dues before: ' . $nextPaymentDate 
        : 'Your payments cover until: ' . $nextPaymentDate;

    if ($unpaidMonths > 6) {
        $accountStatus = 'Account Dormant';
    } elseif ($unpaidMonths > 0) {
        $accountStatus = 'Needs to Clear Dues';
    } else {
        $accountStatus = 'Account Active';
    }

    // Manually handle form submission
    if ($request->isMethod('POST')) {
        $currentPassword = $request->request->get('currentPassword');
        $newPassword = $request->request->get('newPassword');

        // Validate current password
        if (!$this->userPasswordHasher->isPasswordValid($user, $currentPassword)) {
            $this->addFlash('error', 'Current password is incorrect.');
        } else {
            // Hash the new password and update the user entity
            $hashedNewPassword = $this->userPasswordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedNewPassword);
            $this->entityManager->flush();

            $this->addFlash('success', 'Password changed successfully.');
            return $this->redirectToRoute('myAccount');
        }
    }

    return $this->render('my_account/index.html.twig', [
        'user' => $user,
        'totalPaid' => $totalPaid,
        'totalDue' => $totalDue,
        'paymentStatus' => $totalPaid >= $totalDue 
            ? 'Paid up-to-date' 
            : 'You have Pending dues',
        'amountOverpaid' => $pendingDues < 0 ? abs($pendingDues) : 0,
        'nextPaymentDate' => $nextPaymentDate,
        'coverageDate' => $coverageDate ? $coverageDate->format('Y-m-d') : 'Any time from Today',
        'remainder' => $totalPaidRemaining,
        'pendingDues' => $pendingDues,
        'message' => $pendingDues > 0 ? 'Please clear your dues' : 'You are up-to-date',
        'messageType' => $pendingDues > 0 ? 'negative' : 'positive',
        'monthsOfNoPayment' => $unpaidMonths,
        'nextPaymentDateMessage' => $nextPaymentDateMessage,
    ]);
}
}