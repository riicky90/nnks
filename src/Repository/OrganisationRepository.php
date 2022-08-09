<?php

namespace App\Repository;

use App\Entity\Organisation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Organisation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Organisation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Organisation[]    findAll()
 * @method Organisation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganisationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Organisation::class);
    }

    public function search($search)
    {
        $qb = $this->createQueryBuilder('o');
        $qb->where('o.Name LIKE :search OR o.City LIKE :search OR o.Email LIKE :search OR o.Phone LIKE :search');
        $qb->setParameter('search', '%'.$search.'%');
        return $qb->getQuery()->getResult();
    }
}
