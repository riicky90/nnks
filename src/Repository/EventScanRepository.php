<?php

namespace App\Repository;

use App\Entity\EventScan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventScan>
 *
 * @method EventScan|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventScan|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventScan[]    findAll()
 * @method EventScan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventScanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventScan::class);
    }

    public function add(EventScan $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EventScan $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function search($search)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.Dancer', 'd');
        $qb->leftJoin('d.team', 't');
        $qb->leftJoin('e.Contest', 'c');
        $qb->leftJoin('e.ScannedBy', 'u');


        $qb->where('t.Name LIKE :search OR c.Name LIKE :search OR u.email LIKE :search');
        $qb->setParameter('search', '%'.$search.'%');
        return $qb->getQuery();
    }


}
