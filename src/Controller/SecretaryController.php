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
            $monthsSinceUpdate = $subscriptionStartDate->diff($currentDate)->y * 12 + $subscriptionStartDate->diff($currentDate)->m;

            // Adjust monthsSinceUpdate to include the current month
            if ($currentDate > $subscriptionStartDate) {
                $monthsSinceUpdate++; // Include the current month in the total
            }

            $totalDue = 0;
            $currentDateIter = clone $subscriptionStartDate;

            // Calculate total due amount
            for ($i = 0; $i < $monthsSinceUpdate; $i++) {
                $monthlyRate = $currentDateIter >= new \DateTimeImmutable('2023-01-01') ? 500 : 300;
                $totalDue += $monthlyRate;

                // Move to the next month
                $currentDateIter = $currentDateIter->modify("+1 month");
            }

            // Calculate how many months the total amount paid covers
            $monthsCovered = 0;
            $totalPaidRemaining = $totalPaid;
            $currentDateIter = clone $subscriptionStartDate;
            while ($totalPaidRemaining > 0) {
                $monthlyRate = $currentDateIter >= new \DateTimeImmutable('2023-01-01') ? 500 : 300;
                if ($totalPaidRemaining >= $monthlyRate) {
                    $monthsCovered++;
                    $totalPaidRemaining -= $monthlyRate;
                } else {
                    break; // No more full months covered
                }
                $currentDateIter = $currentDateIter->modify("+1 month");
            }

            $pendingDues = $totalDue - $totalPaid;
            $unpaidMonths = $pendingDues > 0 ? $monthsSinceUpdate - $monthsCovered : 0;

            // Determine the general status
            if ($pendingDues < 0) {
                $status = 'Over-paid';
                $amount = '+' . number_format(abs($pendingDues));
                $unpaidMonths = 0; // Reset unpaid months if overpaid
            } elseif ($pendingDues > 0) {
                $status = 'Pending Dues';
                $amount = '-' . number_format($pendingDues);
            } else {
                $status = 'All paid';
                $amount = '00';
            }

            // Determine account status
            if ($unpaidMonths > 6) {
                $accountStatus = 'Account Dormant';
            } elseif ($unpaidMonths > 0) {
                $accountStatus = 'Needs to Clear Dues';
            } else {
                $accountStatus = 'Account Active';
            }

            $userData[] = [
                'user' => $user,
                'status' => $status,
                'amount' => $amount,
                'monthsCovered' => $monthsCovered,
                'monthsSinceUpdate' => $monthsSinceUpdate,
                'unpaidMonths' => $unpaidMonths,
                'accountStatus' => $accountStatus, // New field for account status
            ];
        }

        // Filter user data based on status
        if ($statusFilter !== 'all') {
            $userData = array_filter($userData, fn($data) => $data['status'] === $statusFilter);
        }

        // Define the order of statuses
        $statusOrder = [
            'All paid' => 1,
            'Over-paid' => 2,
            'Pending Dues' => 3,
            'Account Dormant' => 4,
            'Needs to Clear Dues' => 5,
            'Account Active' => 6,
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
