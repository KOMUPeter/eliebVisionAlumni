<?php

namespace App\Controller;

use App\Entity\Payout;
use App\Form\PayoutType;
use App\Repository\UsersRepository;
use App\Repository\PayoutRepository;
use App\Repository\EventsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TreasurerController extends AbstractController
{
    private UsersRepository $usersRepository;
    private PayoutRepository $payoutRepository;
    private EventsRepository $eventsRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UsersRepository $usersRepository,
        PayoutRepository $payoutRepository,
        EventsRepository $eventsRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->usersRepository = $usersRepository;
        $this->payoutRepository = $payoutRepository;
        $this->eventsRepository = $eventsRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/balance', name: 'balance')]
    public function showBalance(): Response
    {
        $totalRegistrationAmount = $this->usersRepository->getTotalRegistrationAmount();
        $totalPayoutAmount = $this->payoutRepository->getTotalPayoutAmount();
        $totalEventsCost = $this->eventsRepository->getTotalEventCost();

        // Step 1: Retrieve deactivated users
        $deactivatedUsers = $this->usersRepository->findDeactivatedUsers();

        // Step 2: Calculate total refund amount
        $totalRefundAmount = 0;
        foreach ($deactivatedUsers as $user) {
            // You can reuse the logic from RefundOverviewController to calculate the refund amount
            $registrationDate = $user->getUpdatedAt();
            $deactivationDate = $user->getDeactivationDate();
            $todaysDate = new \DateTimeImmutable();

            if ($deactivationDate) {
                if (!$deactivationDate instanceof \DateTimeImmutable) {
                    $deactivationDate = \DateTimeImmutable::createFromMutable($deactivationDate);
                }

                $refundDate = $deactivationDate->add(new \DateInterval('P3M'));
            } else {
                $refundDate = null;
            }

            $totalPayouts = $this->payoutRepository->calculateTotalPayoutsForUser($user);
            $coveredUntilDate = $this->calculateCoveredUntilDate($registrationDate, $totalPayouts);

            $overpaidAmount = $coveredUntilDate > $todaysDate
                ? $this->calculateOverpaidAmount($todaysDate, $coveredUntilDate)
                : 0;

            $amountPaidUntilToday = $this->calculateAmountPaidUntilDate($registrationDate, $todaysDate);

            $refundAmount = $overpaidAmount > 0
                ? (0.8 * $amountPaidUntilToday) + $overpaidAmount
                : 0.8 * $totalPayouts;

            $totalRefundAmount += $refundAmount;
        }

        // Step 3: Subtract total refund amount from balance
        $balance = $totalRegistrationAmount + $totalPayoutAmount - $totalEventsCost - $totalRefundAmount;

        return $this->render('treasurer/index.html.twig', [
            'balance' => [
                'totalRegistrations' => $totalRegistrationAmount,
                'totalMonthlyPayments' => $totalPayoutAmount,
                'totalEventsCost' => $totalEventsCost,
                'totalRefundAmount' => $totalRefundAmount, // Include total refund amount in the view
                'balance' => $balance,
            ],
            'form' => $this->createForm(PayoutType::class, new Payout())->createView(),
            'payouts' => [], // Placeholder for payouts, to be filled by the combined route
            'sortField' => 'user', // Default sort field
            'sortDirection' => 'asc', // Default sort direction
        ]);
    }

    #[Route('/add-payout', name: 'add_payout')]
    public function addPayout(Request $request): Response
    {
        $payout = new Payout();
        return $this->handlePayoutForm($request, $payout, false);
    }

    #[Route('/edit-payout/{id}', name: 'edit_payout')]
    public function editPayout(Request $request, Payout $payout): Response
    {
        return $this->handlePayoutForm($request, $payout, true);
    }
    
    private function handlePayoutForm(Request $request, Payout $payout, bool $isEdit): Response
    {
        $form = $this->createForm(PayoutType::class, $payout);
        $form->handleRequest($request);

        // Retrieve sorting, search, and showAll parameters from the request
        $sortField = $request->query->get('sortField', 'firstName');
        $sortDirection = $request->query->get('sortDirection', 'asc');
        $searchTerm = $request->query->get('searchTerm', '');
        $showAll = (bool) $request->query->get('showAll', false); // Convert to boolean

        // Validate the sortField
        if (!in_array($sortField, ['firstName', 'lastName'], true)) {
            $sortField = 'firstName';
        }

        // Create the query builder for fetching sorted and filtered payments
        $queryBuilder = $this->entityManager->getRepository(Payout::class)
            ->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->addSelect('u');

        // Apply search filter if search term is provided
        if ($searchTerm) {
            $queryBuilder->andWhere('u.firstName LIKE :searchTerm OR u.lastName LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        // Apply sorting
        $queryBuilder->orderBy('u.' . $sortField, $sortDirection);

        // Apply pagination
        if (!$showAll) {
            $queryBuilder->setMaxResults(10); // Show only the first 10 items
        }

        $payments = $queryBuilder->getQuery()->getResult();

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->persist($payout);
                $this->entityManager->flush();
                if ($isEdit) {
                    $this->addFlash('success', 'Payment updated successfully.');
                } else {
                    $this->addFlash('success', 'Payment added successfully.');
                }                return $this->redirectToRoute('add_payout');
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
            }
        }

        return $this->render('treasurer/add_payout.html.twig', [
            'form' => $form->createView(),
            'payments' => $payments,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'searchTerm' => $searchTerm,
            'showAll' => $showAll,
            'isEdit' => $isEdit, 
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
}
