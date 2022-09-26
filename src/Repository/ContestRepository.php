<?php

namespace App\Repository;

use App\Entity\Contest;
use Craue\ConfigBundle\Util\Config;
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
    private $config;

    public function __construct(ManagerRegistry $registry, Config $config)
    {
        $this->config = $config;
        parent::__construct($registry, Contest::class);
    }


    public function allOpenContests()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.Enabled = :val')
            ->setParameter('val', true)
            ->andWhere('c.Date > :date')
            ->setParameter('date', new \DateTime())
            ->orderBy('c.Date', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function todayContest()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.Enabled = :val')
            ->setParameter('val', true)
            ->andWhere('c.Date = :date')
            ->setParameter('date', (new \DateTime())->format('Y-m-d'))
            ->orderBy('c.Date', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function search($search)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->join('c.Organisation', 'o');
        $qb->where('c.Name LIKE :search OR c.Location LIKE :search OR c.Description LIKE :search OR o.Name LIKE :search OR c.Disciplines LIKE :search');
        $qb->setParameter('search', '%'.$search.'%');
        $qb->orderBy('c.id', 'DESC');
        return $qb->getQuery();
    }
}
