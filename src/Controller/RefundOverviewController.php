<?php
// src/Controller/RefundOverviewController.php

namespace App\Controller;

use App\Repository\UsersRepository;
use App\Repository\PayoutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \DateTimeImmutable;

class RefundOverviewController extends AbstractController
{
    private UsersRepository $usersRepository;
    private PayoutRepository $payoutRepository;

    public function __construct(
        UsersRepository $usersRepository,
        PayoutRepository $payoutRepository
    ) {
        $this->usersRepository = $usersRepository;
        $this->payoutRepository = $payoutRepository;
    }

    #[Route('refund_overview/adjust-finances', name: 'adjust_finances')]
    public function adjustFinances(): Response
    {
        $deactivatedUsers = $this->usersRepository->findDeactivatedUsers();
        $reactivatedUsers = $this->usersRepository->findReactivatedUsers();
    
        $userStats = [];
        $totalRefundAmount = 0; // Initialize total refund amount
    
        foreach ($deactivatedUsers as $user) {
            $registrationDate = $user->getUpdatedAt(); // This could be DateTime or DateTimeImmutable
            $deactivationDate = $user->getDeactivationDate(); // Assuming this is also DateTime or DateTimeImmutable
            $todaysDate = new \DateTimeImmutable();
    
            // Check if deactivation date is valid
            if ($deactivationDate) {
                // Ensure deactivationDate is an instance of DateTimeImmutable
                if (!$deactivationDate instanceof \DateTimeImmutable) {
                    $deactivationDate = \DateTimeImmutable::createFromMutable($deactivationDate);
                }
                
                // Calculate the refund date as 3 months after deactivation date
                $refundDate = $deactivationDate->add(new \DateInterval('P3M'));
            } else {
                $refundDate = null;
            }
    
            // Use the repository method to get total payouts for this specific user
            $totalPayouts = $this->payoutRepository->calculateTotalPayoutsForUser($user);
    
            // Calculate the date until which the total payouts cover
            $coveredUntilDate = $this->calculateCoveredUntilDate($registrationDate, $totalPayouts);
    
            // Calculate the overpaid amount if Covered Until Date is after Today's Date
            $overpaidAmount = $coveredUntilDate > $todaysDate
                ? $this->calculateOverpaidAmount($todaysDate, $coveredUntilDate)
                : 0;
    
            // Calculate the amount paid until today's date
            $amountPaidUntilToday = $this->calculateAmountPaidUntilDate($registrationDate, $todaysDate);
    
            // Calculate the refund amount
            $refundAmount = $overpaidAmount > 0
                ? (0.8 * $amountPaidUntilToday) + $overpaidAmount
                : 0.8 * $totalPayouts;
    
            // Update total refund amount
            $totalRefundAmount += $refundAmount;
    
            $userStats[] = [
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'months_registered' => $this->calculateMonthsDifference($registrationDate, $deactivationDate),
                'total_payouts' => $totalPayouts,
                'covered_until_date' => $coveredUntilDate->format('Y-m-d'),
                'todaysDate' => $todaysDate->format('Y-m-d'),
                'amount_paid_until_today' => $amountPaidUntilToday,
                'overpaid_amount' => $overpaidAmount,
                'refund_amount' => $refundAmount,
                'deactivation_date' => $deactivationDate ? $deactivationDate->format('Y-m-d') : 'N/A',
                'refund_date' => $refundDate ? $refundDate->format('Y-m-d') : 'N/A',
            ];
        }
    
        return $this->render('refund_overview/adjust_finances.html.twig', [
            'userStats' => $userStats,
            'reactivatedUsers' => $reactivatedUsers,
            'deactivatedUsers' => $deactivatedUsers,
            'totalRefundAmount' => $totalRefundAmount, // Pass the total refund amount to Twig
        ]);
    }

    private function calculateCoveredUntilDate(\DateTimeInterface $startDate, float $totalPayouts): \DateTimeImmutable
    {
        $date = $startDate instanceof \DateTimeImmutable ? $startDate : \DateTimeImmutable::createFromMutable($startDate);
        $remainingAmount = $totalPayouts;

        // Before January 2023
        while ($remainingAmount > 0 && $date < new \DateTimeImmutable('2023-01-01')) {
            $remainingAmount -= 300;
            if ($remainingAmount > 0) {
                $date = $date->modify('+1 month');
            }
        }

        // From January 2023 onwards
        while ($remainingAmount > 0) {
            $remainingAmount -= 500;
            if ($remainingAmount > 0) {
                $date = $date->modify('+1 month');
            }
        }

        return $date;
    }

    private function calculateOverpaidAmount(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): float
    {
        $totalOverpaid = 0;
        $date = $startDate;

        while ($date <= $endDate) {
            if ($date < new \DateTimeImmutable('2023-01-01')) {
                $totalOverpaid += 300;
            } else {
                $totalOverpaid += 500;
            }
            $date = $date->modify('+1 month');
        }

        return $totalOverpaid;
    }

    private function calculateAmountPaidUntilDate(\DateTimeInterface $startDate, \DateTimeImmutable $endDate): float
    {
        $totalPaid = 0;
        $date = $startDate instanceof \DateTimeImmutable ? $startDate : \DateTimeImmutable::createFromMutable($startDate);

        while ($date <= $endDate) {
            if ($date < new \DateTimeImmutable('2023-01-01')) {
                $totalPaid += 300;
            } else {
                $totalPaid += 500;
            }
            $date = $date->modify('+1 month');
        }

        return $totalPaid;
    }

    private function calculateMonthsDifference(\DateTimeInterface $startDate, \DateTimeInterface $endDate): int
    {
        $interval = $startDate->diff($endDate);
        return ($interval->y * 12) + $interval->m;
    }
}
