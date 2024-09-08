<?php


namespace App\Repository;

use App\Entity\Payout;
use App\Entity\Users; // Ensure this is the correct namespace
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

    public function getTotalPayoutAmount(): int
    {
        return (int) $this->createQueryBuilder('p')
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

    public function calculateTotalPayoutsForUser(Users $user): int
    {
        // Cast result to int to ensure return type is correct
        $totalPayout = $this->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->where('p.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        // Return 0 if no result is found
        return $totalPayout !== null ? (int) $totalPayout : 0;
    }

    public function calculateTotalPayoutsForDeactivatedUsers(array $deactivatedUsers): int
    {
        if (empty($deactivatedUsers)) {
            return 0;
        }

        $userIds = array_map(fn($user) => $user->getId(), $deactivatedUsers);

        // Query to calculate the total payout for the given user IDs
        $totalPayout = $this->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->where('p.user IN (:userIds)')
            ->setParameter('userIds', $userIds)
            ->getQuery()
            ->getSingleScalarResult();

        return $totalPayout !== null ? (int) $totalPayout : 0;
    }

}
