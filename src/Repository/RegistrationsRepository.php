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
            ->leftJoin('r.Teams', 't')
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

    public function findByNot($field, $value)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where($qb->expr()->not($qb->expr()->eq('a.' . $field, '?1')));
        $qb->setParameter(1, $value);

        return $qb->getQuery()->getResult();
    }

    //get all teams form specific organisation that are not yet registered for the current contest
    public function getTeamsNotRegistered($organisation, $contest)
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.Registrations', 'r')
            ->andWhere('t.Organisation = :org')
            ->andWhere('r.Contest = :contest')
            ->setParameter('org', $organisation)
            ->setParameter('contest', $contest)
            ->getQuery()
            ->getResult();
    }

    //search function for registrations
    public function search($search)
    {
        $qb = $this->createQueryBuilder('r');
        $qb->leftJoin('r.Team', 't');
        $qb->leftJoin('r.Contest', 'c');
        $qb->where('t.Name LIKE :search OR c.Name LIKE :search OR r.id LIKE :search')
            ->setParameter('search', '%' . $search . '%');
        $qb->setParameter('search', '%'.$search.'%');

        $qb->orderBy('r.createdAt', 'DESC');

        return $qb->getQuery();
    }

    //search function for registrations
    public function searchPersonal($search, $user)
    {
        $qb = $this->createQueryBuilder('r');
        $qb->leftJoin('r.Team', 't');
        $qb->leftJoin('r.Contest', 'c');
        $qb->andWhere('t.Name LIKE :search OR c.Name LIKE :search');
        $qb->andWhere('t.User = :user');
        $qb->setParameter('search', '%'.$search.'%');
        $qb->setParameter('user', $user);
        $qb->orderBy('r.createdAt', 'DESC');

        return $qb->getQuery();
    }

}
