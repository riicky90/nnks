<?php

namespace App\Repository;

use App\Entity\Dancers;
use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\Array_;

/**
 * @method Dancers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dancers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dancers[]    findAll()
 * @method Dancers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DancersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dancers::class);
    }

    public function findByUser($user, $search): int|array|string
    {
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.Teams', 't')
            ->andWhere('t.User = :user')
            ->andWhere('d.FirstName LIKE :search OR d.LastName LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->setParameter('user', $user)
            ->orderBy('d.id', 'ASC')
            ->getQuery();

        return $qb->getResult();
    }
}
