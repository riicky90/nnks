<?php

namespace App\Autocompleter;

use App\Entity\Registrations;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;
use Symfony\UX\Autocomplete\EntityAutocompleterInterface;

class SearchFilter implements EntityAutocompleterInterface
{
    public function getEntityClass(): string
    {
        return Registrations::class;
    }

    public function createFilteredQueryBuilder(EntityRepository $repository, string $query): QueryBuilder
    {
        return $repository
            // the alias "food" can be anything
            ->createQueryBuilder('r')
            ->leftJoin('r.Team', 't')
            ->andWhere('t.Name LIKE :search')
            ->setParameter('search', '%'.$query.'%');
    }

    public function getLabel(object $entity): string
    {
        return $entity->getTeam()->getName();
    }

    public function getValue(object $entity): string
    {
        return $entity->getTeam()->getId();
    }

    public function isGranted(Security $security): bool
    {
        return true;
    }
}