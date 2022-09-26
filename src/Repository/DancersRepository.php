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

    public function findByUser($user): int|array|string
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.team', 'o')
            ->andWhere('o.User = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }
}
