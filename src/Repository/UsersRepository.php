<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Users>
 * 
 * @method Adresse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adresse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adresse[]    findAll()
 * @method Adresse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Users) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findUserByEmailOrUsername(string $useremail) : ?Users
    {
        return $this->createQueryBuilder('U')
            ->where('U.email = :indentifier')
            ->setParameter('indentifier', $useremail )
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }

    //calculate the total registration
    public function getTotalRegistrationAmount(): int
    {
        return $this->createQueryBuilder('u')
            ->select('SUM(u.registrationAmount)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // public function findUsersWithRefunds(): array
    // {
    //     return $this->createQueryBuilder('u')
    //         ->where('u.outstandingAmount > 0')
    //         ->getQuery()
    //         ->getResult();
    // }

    public function findDeactivatedUsers(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.deactivationDate IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    public function findReactivatedUsers(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.reactivationDate IS NOT NULL')
            ->getQuery()
            ->getResult();
    }



    //    public function findOneBySomeField($value): ?Users
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
