<?php

namespace App\Controller;

use App\Entity\EventScan;
use App\Repository\ContestRepository;
use App\Repository\DancersRepository;
use App\Repository\EventScanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventScanController extends AbstractController
{

    #[Route('/eventscan', name: 'eventscan_index')]
    public function scan(Request $request, PaginatorInterface $paginator, ContestRepository $contestRepository, EventScanRepository $eventScanRepository)
    {
        $contest = $contestRepository->todayContest();

        if ($contest == null) {
            return $this->render('theme/message.html.twig', [
                'title' => 'Event scan',
                'message' => 'Geen event gevonden voor vandaag',
            ]);
        }

        $search = $request->query->get('search');

        $eventScans = $eventScanRepository->search($search);

        $pagination = $paginator->paginate(
            $eventScans,
            $request->query->getInt('page', 1),
            12
        );


        return $this->render('eventscan/index.html.twig', [
            'contest' => $contest,
            'eventscans' => $pagination,
            'filter' => null,
        ]);
    }

    #[Route('/eventscan/scan/{contest}/{dancer}', name: 'eventscan_scan')]
    public function show(ContestRepository $contestRepository, EntityManagerInterface $entityManager, DancersRepository $dancersRepository, $contest, $dancer)
    {
        $todayContest = $contestRepository->todayContest();

        if ($todayContest == null) {
            return new Response('Geen event gevonden voor vandaag');
        }
        $dancer = $dancersRepository->find($dancer);
        $contest = $contestRepository->find($contest);

        if ($todayContest != $contest) {
            return new Response('Dit event is niet vandaag');
        }

        if ($contest->getDate()->format('Y-m-d') != date('Y-m-d')) {
            return new Response('Dit event is niet vandaag');
        }


        return $this->render('eventscan/scan.html.twig', [
            'dancer' => $dancer,
            'contest' => $contest,
        ]);

    }

    #[Route('/eventscan/save/{contest}/{dancer}', name: 'eventscan_save')]
    public function save(ContestRepository $contestRepository, EntityManagerInterface $entityManager, DancersRepository $dancersRepository, EventScanRepository $eventScanRepository, $contest, $dancer)
    {
        $dancer = $dancersRepository->find($dancer);
        $contest = $contestRepository->find($contest);
        $message = [];
        if ($dancer != null && $contest != null) {

            if ($eventScanRepository->findBy(['Dancer' => $dancer->getId(), 'Contest' => $contest->getId()]) == null) {
                $eventScan = new EventScan();
                $eventScan->setContest($contest);
                $eventScan->setDancer($dancer);

                $entityManager->persist($eventScan);
                $entityManager->flush();

                $message = [
                    'success' => true,
                    'message' => 'Danser ingechecked',
                ];

            } else {
                $message = [
                    'success' => false,
                    'message' => '!! - Danser is al ingechecked - !!',
                ];
            }

            return $this->render('eventscan/scan_result.html.twig', [
                'dancer' => $dancer,
                'result' => $message,
            ]);
        }

        return $this->render('eventscan/scan.html.twig', [
            'dancer' => $dancer,
            'contest' => $contest,
        ]);

    }

}