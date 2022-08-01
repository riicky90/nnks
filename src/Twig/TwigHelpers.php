<?php

namespace App\Twig;

use App\Entity\Contest;
use App\Entity\Registrations;
use App\Repository\RegistrationsRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use App\Entity\Team;

class TwigHelpers extends AbstractExtension
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('registrationsCount', [$this, 'countRegistrations'])
        ];
    }

    public function countRegistrations(): string
    {
        $repo = $this->em->getRepository(Registrations::class);

        return $repo->createQueryBuilder('r')
            ->select('count(r.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}