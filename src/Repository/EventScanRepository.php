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


}
