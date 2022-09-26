<?php

namespace App\Controller;

use App\Repository\ContestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    #[Route('/contests', name: 'api_contests_index', methods: ['GET'])]
    public function index(ContestRepository $contestRepository): Response
    {
        $contests = $contestRepository->allOpenContests();
        $items_json = [];
        foreach ($contests as $contest) {
            $items_json[] =
                [
                    'id' => $contest->getId(),
                    'name' => $contest->getName(),
                    'start' => $contest->getDate()->format('Y-m-d H:i:s'),
                    'description' => $contest->getDescription(),
                    'enabled' => $contest->getEnabled(),
                    'location' => $contest->getLocation(),
                    'registrationFee' => $contest->getRegistrationFee(),
                    'entranceFee' => $contest->getEntranceFee(),
                    'single_event_link' => '/contest/' . $contest->getId().'/'. $contest->getName(),
                    'diciplines' => $contest->getDisciplines(),
                    'organisation' => array([
                        'id' => $contest->getOrganisation()->getId(),
                        'name' => $contest->getOrganisation()->getName(),
                        'description' => $contest->getOrganisation()->getDescription(),
                        'email' => $contest->getOrganisation()->getEmail(),
                        'phone' => $contest->getOrganisation()->getPhone(),
                    ]),
                ];
        }

        $info = [
            'total' => count($items_json),
            'items' => $items_json,

        ];

        return $this->json($info);

    }
}