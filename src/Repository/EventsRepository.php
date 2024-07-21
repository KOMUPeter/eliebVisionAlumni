<?php

namespace App\Repository;

use App\Entity\Events;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Events>
 */
class EventsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Events::class);
    }

    public function getTotalEventCost(): int
    {
        return $this->createQueryBuilder('e')
            ->select('SUM(e.cost)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAllEvents(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.eventDate', 'DESC') // Order by eventDate in descending order
            ->getQuery()
            ->getResult();
    }
}
