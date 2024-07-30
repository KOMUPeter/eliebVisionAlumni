<?php

namespace App\Controller;

use App\Entity\Events;
use App\Form\EventType;
use App\Repository\EventsRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class EventsController extends AbstractController
{
    private $eventsRepository;
    private $entityManager;
    private $usersRepository;

    public function __construct(EventsRepository $eventsRepository, EntityManagerInterface $entityManager, UsersRepository $usersRepository)
    {
        $this->eventsRepository = $eventsRepository;
        $this->entityManager = $entityManager;
        $this->usersRepository = $usersRepository;
    }

    #[Route('/events', name: 'events_list')]
    public function listEvents(Request $request): Response
    {
        $events = $this->eventsRepository->getAllEvents();
        
        // Create a new event and form only if the user has the 'ROLE_TREASURE' role
        $form = null;
        if ($this->isGranted('ROLE_TREASURER') || $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SECRETARY')) {
            $event = new Events();
            $form = $this->createForm(EventType::class, $event);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->entityManager->persist($event);
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Event has been added');
                    return $this->redirectToRoute('events_list');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
                }
            }
        }

        return $this->render('events/index.html.twig', [
            'events' => $events,
            'form' => $form ? $form->createView() : null, // Pass form view if it exists
        ]);
    }
}

