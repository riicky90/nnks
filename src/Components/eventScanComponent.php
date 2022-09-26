<?php

namespace App\Components;

use App\Repository\ContestRepository;
use App\Repository\EventScanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('eventscan')]
class eventScanComponent extends AbstractController
{
    use DefaultActionTrait;

    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function RenderTable()
    {
        $entityManager = $this->doctrine->getManager();

        $contest = $entityManager->getRepository(ContestRepository::class)->todayContest();
        $eventScans = $entityManager->getRepository(EventScanRepository::class)->findBy(['Contest' => $contest]);

        if ($contest == null) {
            return $this->render('theme/message.html.twig', [
                'title' => 'Event scan',
                'message' => 'Geen event gevonden voor vandaag',
            ]);
        }

        return $this->render('eventscan/_list.html.twig', [
            'contest' => $contest,
            'eventscans' => $eventScans,
        ]);
    }
}