<?php

namespace App\Repository;

use App\Entity\Organisation;
use App\Entity\Registrations;
use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Registrations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Registrations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Registrations[]    findAll()
 * @method Registrations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegistrationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Registrations::class);
    }

    public function chartDates()
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.createdAt', 'DESC')
            ->select('count(r.id) as count', "DATE_FORMAT(r.createdAt,'%d-%m-%Y') as formatted_date")
            ->addGroupBy('formatted_date')
            ->groupBy('formatted_date')
            ->getQuery()
            ->setMaxResults(7)
            ->getScalarResult();
    }

    public function totalRegistrations()
    {
        return $this->createQueryBuilder('r',)
            ->select('count(r.id)')
            ->getQuery()
            ->getSingleScalarResult();

    }

    public function activeRegistrations($organisation)
    {
        return $this->createQueryBuilder('r',)
            ->leftJoin('r.Team', 't')
            ->andWhere('t.Organisation = :org')
            ->setParameter('org', $organisation)
            ->getQuery()
            ->getScalarResult();

    }

    public function totalPersonalRegistrations($organisation)
    {
        return $this->createQueryBuilder('r',)
            ->leftJoin('r.Team', 't')
            ->andWhere('t.Organisation = :org')
            ->select('count(r.id)')
            ->setParameter('org', $organisation)
            ->getQuery()
            ->getSingleScalarResult();

    }

    public function personalRegistrations($organisation)
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.Team', 't')
            ->andWhere('t.Organisation = :val')
            ->setParameter('val', $organisation)
            ->getQuery()
            ;
    }

    public function checkIfTeamRegistered($team, $contest)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.Team = :team')
            ->andWhere('r.Contest = :contest')
            ->setParameter('team', $team)
            ->setParameter('contest', $contest)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
