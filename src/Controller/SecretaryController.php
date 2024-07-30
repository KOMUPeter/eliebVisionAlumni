<?php
// src/Controller/SecretaryController.php
namespace App\Controller;

use App\Entity\Payout;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecretaryController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/secretary', name: 'secretary_users')]
    public function listUsers(Request $request): Response
    {
        $statusFilter = $request->query->get('status', 'all');

        // Retrieve all users
        $users = $this->entityManager->getRepository(Users::class)->findAll();

        // Prepare user data with payment status
        $userData = [];

        foreach ($users as $user) {
            $payouts = $this->entityManager->getRepository(Payout::class)->findBy(['user' => $user], ['month' => 'ASC']);
            $totalPaid = array_sum(array_map(fn($payout) => $payout->getAmount(), $payouts));

            $subscriptionStartDate = $user->getUpdatedAt();
            $currentDate = new \DateTimeImmutable();
            $monthsSubscribed = $subscriptionStartDate->diff($currentDate)->y * 12 + $subscriptionStartDate->diff($currentDate)->m;

            $totalDue = 0;
            $unpaidMonths = 0;

            $currentDateIter = clone $subscriptionStartDate;

            // Iterate through each month from the start date to the current date
            for ($i = 0; $i <= $monthsSubscribed; $i++) {
                $monthlyRate = $currentDateIter >= new \DateTimeImmutable('2023-01-01') ? 500 : 300;
                $totalDue += $monthlyRate;

                // Check if there's a payment for this month
                $monthPayouts = array_filter($payouts, fn($payout) => $payout->getMonth()->format('Y-m') === $currentDateIter->format('Y-m'));
                $monthPaid = array_sum(array_map(fn($payout) => $payout->getAmount(), $monthPayouts));

                if ($monthPaid < $monthlyRate) {
                    $unpaidMonths++;
                }

                $currentDateIter = $currentDateIter->modify("+1 month");
            }

            $pendingDues = $totalDue - $totalPaid;

            // Determine status and handle overpayment
            if ($pendingDues < 0) {
                $status = 'Over-paid';
                $amount = '+' . number_format(abs($pendingDues));
                $unpaidMonths = 0; // No unpaid months if overpaid
            } elseif ($pendingDues > 0) {
                $status = $unpaidMonths > 6 ? 'Account Dormant' : 'Pending Dues';
                $amount = '-' . number_format($pendingDues);
            } else {
                $status = 'All months paid';
                $amount = '00';
                $unpaidMonths = 0; // Reset to 0 if all months are paid
            }

            $userData[] = [
                'user' => $user,
                'status' => $status,
                'amount' => $amount,
                'unpaidMonths' => $unpaidMonths,
            ];
        }

        // Filter user data based on status
        if ($statusFilter !== 'all') {
            $userData = array_filter($userData, fn($data) => $data['status'] === $statusFilter);
        }

        // Define the order of statuses
        $statusOrder = [
            'All months paid' => 1,
            'Over-paid' => 2,
            'Pending Dues' => 3,
            'Account Dormant' => 4,
        ];

        // Sort the userData array by status
        usort($userData, function($a, $b) use ($statusOrder) {
            return $statusOrder[$a['status']] <=> $statusOrder[$b['status']];
        });

        return $this->render('secretary/index.html.twig', [
            'userData' => $userData,
            'statusFilter' => $statusFilter,
        ]);
    }
}
