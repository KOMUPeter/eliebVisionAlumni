<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use App\Repository\PayoutRepository;
use App\Repository\EventsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TreasurerController extends AbstractController
{
    private $usersRepository;
    private $payoutRepository;
    private $eventsRepository; 

    public function __construct(
        UsersRepository $usersRepository,
        PayoutRepository $payoutRepository,
        EventsRepository $eventsRepository
    ) {
        $this->usersRepository = $usersRepository;
        $this->payoutRepository = $payoutRepository;
        $this->eventsRepository = $eventsRepository;
    }

    #[Route('/balance', name: "balance")]
    public function showBalance(): Response
    {
        $totalRegistrationAmount = $this->usersRepository->getTotalRegistrationAmount();
        $totalPayoutAmount = $this->payoutRepository->getTotalPayoutAmount();
        $totalEventsCost = $this->eventsRepository->getTotalEventCost();

        $balance = $totalRegistrationAmount + $totalPayoutAmount - $totalEventsCost;

        return $this->render('treasurer/index.html.twig', [
            'balance' => [
                'totalRegistrations' => $totalRegistrationAmount,
                'totalMonthlyPayments' => $totalPayoutAmount,
                'totalEventsCost' => $totalEventsCost,
                'balance' => $balance,
            ],
        ]);
    }
}
