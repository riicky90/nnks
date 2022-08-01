<?php

namespace App\Repository;

use App\Entity\Contest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contest|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contest|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contest[]    findAll()
 * @method Contest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contest::class);
    }


    public function allOpenContests()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.Enabled = :val')
            ->setParameter('val', true)
            ->orderBy('c.Date', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Contest
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
