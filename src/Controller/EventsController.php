<?php

namespace App\Controller;

use App\Repository\EventsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventsController extends AbstractController
{
    private $eventsRepository;

    public function __construct(EventsRepository $eventsRepository)
    {
        $this->eventsRepository = $eventsRepository;
    }

    #[Route('/events', name: 'events_list')]
    public function listEvents(): Response
    {
        $events = $this->eventsRepository->getAllEvents();
        return $this->render('events/index.html.twig', [
            'events' => $events,
        ]);
    }
}
