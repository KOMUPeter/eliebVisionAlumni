<?php

namespace App\Repository;

use App\Entity\Payout;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payout>
 */
class PayoutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payout::class);
    }

    public function getTotalPayoutAmount(): float
    {
        return $this->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findPayoutsSortedByUser($sortField, $sortDirection)
    {
        return $this->createQueryBuilder('p')
            ->join('p.user', 'u')
            ->orderBy('u.' . $sortField, $sortDirection)
            ->getQuery()
            ->getResult();
    }
    
    public function calculateTotalPayoutsSince(\DateTimeInterface $sinceDate): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->where('p.month >= :sinceDate')
            ->setParameter('sinceDate', $sinceDate);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

}
