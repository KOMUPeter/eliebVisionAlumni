<?php
namespace App\DataFixtures;

use App\Entity\Events;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $eventDatas = [
            [
                'title' => 'Annual Meeting',
                'description' => 'To discuss about the new arrivals and proceedings of the group',
                'eventDate' => \DateTime::createFromFormat('Y-m-d H:i:s', '2025-05-23 10:00:00'),
                'cost' => 25000,
                'type' => 'GroupHappenings', // Ensure this value matches the choices in your entity
                'personInvolved' => 'All members', // Ensure this value is correctly set
            ],
            [
                'title' => 'Quarterly Review',
                'description' => 'Quarterly performance review and strategy planning',
                'eventDate' => \DateTime::createFromFormat('Y-m-d H:i:s', '2023-08-15 14:00:00'),
                'cost' => 40000,
                'type' => 'Disbursement', // Ensure this value matches the choices in your entity
                'personInvolved' => 'Finance Team', // Ensure this value is correctly set
            ],
        ];

        foreach ($eventDatas as $eventData) {
            $event = new Events();
            $event->setTitle($eventData['title']);
            $event->setDescription($eventData['description']);
            $event->setEventDate($eventData['eventDate']);
            $event->setCost($eventData['cost']);
            $event->setType($eventData['type']); // Set type based on the defined choices
            $event->setPersonInvolved($eventData['personInvolved']); // Set personInvolved
            $manager->persist($event);
        }

        $manager->flush();
    }
}
