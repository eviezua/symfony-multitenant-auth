<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Account>
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    public function findFirstAccountForUser(User $user): ?Account
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.users', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId())
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function userHasAccess(User $user, int $accountId): bool
    {
        $count = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->innerJoin('a.users', 'u')
            ->where('u = :user')
            ->andWhere('a.id = :accountId')
            ->setParameter('user', $user)
            ->setParameter('accountId', $accountId)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}
