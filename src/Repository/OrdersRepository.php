<?php

namespace App\Repository;

use App\Entity\Orders;
use App\Entity\Registrations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Orders|null find($id, $lockMode = null, $lockVersion = null)
 * @method Orders|null findOneBy(array $criteria, array $orderBy = null)
 * @method Orders[]    findAll()
 * @method Orders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orders::class);
    }

    public function orderTotal(Registrations $registration)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.Registration = :registration')
            ->setParameter('registration', $registration)
            ->select('SUM(o.Amount) as orderTotal')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function personalOrder($organisation)
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.Registration', 'r')
            ->leftJoin('r.Team', 't')
            ->andWhere('t.Organisation = :val')
            ->setParameter('val', $organisation)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    //get last 10 perosnal orders for this user
    public function personalOrderLastTen($user)
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.Registration', 'r')
            ->leftJoin('r.Team', 't')
            ->andWhere('t.User = :val')
            ->setParameter('val', $user)
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Orders
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
