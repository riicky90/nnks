<?php

namespace App\Repository;

use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    public function personalTeams($organisation)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.Organisation = :val')
            ->setParameter('val', $organisation)
            ->getQuery()
            ->getResult()
        ;
    }

    public function totalTeams()
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->getQuery()
            ->getSingleScalarResult()
            ;

    }

    public function totalPersonalTeams($organisation)
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->andWhere('t.Organisation = :val')
            ->setParameter('val', $organisation)
            ->getQuery()
            ->getSingleScalarResult()
            ;

    }

    public function searchPersonalTeam($organisation, $query): \Doctrine\ORM\Query
    {

        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.Trainer', 'trainer')
            ->andWhere('t.Organisation = :organisation')
            ->setParameter('organisation', $organisation)
        ;

        if($query)
        {
            $qb->andWhere('t.Name LIKE :query OR t.MailTrainer LIKE :query OR trainer.FirstName LIKE :query')
                ->setParameter('query', '%'.$query.'%');
        }

        return $qb->getQuery();
    }

}
