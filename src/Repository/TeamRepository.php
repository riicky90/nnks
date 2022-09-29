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
            ->getResult();
    }

    public function totalTeams()
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->getQuery()
            ->getSingleScalarResult();

    }

    public function totalPersonalTeams($organisation)
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->andWhere('t.Organisation = :val')
            ->setParameter('val', $organisation)
            ->getQuery()
            ->getSingleScalarResult();

    }

    public function searchPersonalTeam($user, $query): \Doctrine\ORM\Query
    {

        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.User', 'user')
            ->andWhere('t.User = :user')
            ->setParameter('user', $user);

        if ($query) {
            $qb->andWhere('t.Name LIKE :query OR t.MailTrainer LIKE :query OR user.email LIKE :query')
                ->setParameter('query', '%' . $query . '%');
        }

        return $qb->getQuery();
    }

    //empty function
    public function findUnregisteredTeams($organisation, $contest)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->leftJoin('t.Organisation', 'o');
        $expr = $this->getEntityManager()->getExpressionBuilder();
        $qb->andWhere(
            $expr->notIn(
                't.id',
                $this->getEntityManager()->createQueryBuilder()
                    ->select('identity(tr.Team)')
                    ->from('App:Registrations', 'tr')
                    ->where('tr.Contest = :contest')
                    ->getDQL()
            ));
        $qb->andWhere('o = :organisation');
        $qb->setParameter('organisation', $organisation);
        $qb->setParameter('contest', $contest);

        return $qb;

    }

    public function search($search)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->leftJoin('t.User', 'u');
        $qb->leftJoin('t.Category', 'c');
        $qb->where('t.Name LIKE :search OR t.MailTrainer LIKE :search OR t.TrainerName LIKE :search OR u.DansSchool LIKE :search OR c.Name LIKE :search');
        $qb->setParameter('search', '%'.$search.'%');

        return $qb->getQuery();
    }

    public function searchPersonal($search, $user)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->leftJoin('t.User', 'u');
        $qb->leftJoin('t.Category', 'c');
        $qb->andWhere('t.Name LIKE :search OR t.MailTrainer LIKE :search OR t.TrainerName LIKE :search OR u.DansSchool LIKE :search OR c.Name LIKE :search');
        $qb->andWhere('t.User = :user');
        $qb->setParameter('search', '%'.$search.'%');
        $qb->setParameter('user', $user);
        $qb->orderBy('t.id', 'ASC');

        return $qb->getQuery();
    }

}
