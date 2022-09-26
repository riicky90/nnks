<?php

namespace App\Repository;

use App\Entity\DanceCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DanceCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method DanceCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method DanceCategory[]    findAll()
 * @method DanceCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DanceCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DanceCategory::class);
    }

    // /**
    //  * @return DanceCategory[] Returns an array of DanceCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DanceCategory
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    //search
    public function search($query)
    {
        $qb = $this->createQueryBuilder('c');

        if ($query) {
            $qb->andWhere('c.Name LIKE :val')
                ->setParameter('val', '%'.$query.'%');
        }

        return $qb->getQuery();
    }
}
