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
        $contests = $contestRepository->allOpenContestsApi();
        $items_json = [];
        foreach ($contests as $contest) {
            $regOpen = false;
            if ($contest->getRegistrationOpenFrom() <= new \DateTime()) {
                $regOpen = true;
            }
            $items_json[] = array(
                    'id' => $contest->getId(),
                    'name' => $contest->getName(),
                    'start' => $contest->getDate()->format('Y-m-d H:i:s'),
                    'registrationOpen' => $regOpen,
                    'registrationOpenFrom' => $contest->getRegistrationOpenFrom()->format('d-m-Y'),
                    'description' => $contest->getDescription(),
                    'enabled' => $contest->getEnabled(),
                    'location' => $contest->getLocation(),
                    'locationAddress' => $contest->getLocationAddress(),
                    'locationZipCode' => $contest->getLocationZipCode(),
                    'locationCity' => $contest->getLocationCity(),
                    'registrationFee' => $contest->getRegistrationFee(),
                    'single_event_link' => '/contest/' . $contest->getId() . '/' . $contest->getName(),
                    'disciplines' => $contest->getDisciplines(),
                    'organisation' => array(
                        'id' => $contest->getOrganisation()->getId(),
                        'name' => $contest->getOrganisation()->getName(),
                        'description' => $contest->getOrganisation()->getDescription(),
                        'email' => $contest->getOrganisation()->getEmail(),
                        'phone' => $contest->getOrganisation()->getPhone(),
                        'address' => $contest->getOrganisation()->getAdres(),
                        'zipCode' => $contest->getOrganisation()->getZipCode(),
                        'city' => $contest->getOrganisation()->getCity(),
                    ),
            );
        }

        $info = [
            'total' => count($contests),
            'items' => $items_json,
        ];

        return $this->json($info);

    }

    #[Route('/contest/{id}', name: 'api_contests_show', methods: ['GET'])]
    public function show(ContestRepository $contestRepository, $id): Response
    {
        $contest = $contestRepository->find($id);
        $regOpen = false;
        if ($contest->getRegistrationOpenFrom() <= new \DateTime()) {
            $regOpen = true;
        }
        $item_json =
            [
                'id' => $contest->getId(),
                'name' => $contest->getName(),
                'start' => $contest->getDate()->format('Y-m-d H:i:s'),
                'registrationOpen' => $regOpen,
                'registrationOpenFrom' => $contest->getRegistrationOpenFrom()->format('d-m-Y'),
                'description' => $contest->getDescription(),
                'enabled' => $contest->getEnabled(),
                'location' => $contest->getLocation(),
                'locationAddress' => $contest->getLocationAddress(),
                'locationZipCode' => $contest->getLocationZipCode(),
                'locationCity' => $contest->getLocationCity(),
                'registrationFee' => $contest->getRegistrationFee(),
                'single_event_link' => '/contest/' . $contest->getId() . '/' . $contest->getName(),
                'disciplines' => $contest->getDisciplines(),
                'organisation' => [
                    'id' => $contest->getOrganisation()->getId(),
                    'name' => $contest->getOrganisation()->getName(),
                    'description' => $contest->getOrganisation()->getDescription(),
                    'email' => $contest->getOrganisation()->getEmail(),
                    'phone' => $contest->getOrganisation()->getPhone(),
                    'address' => $contest->getOrganisation()->getAdres(),
                    'zipCode' => $contest->getOrganisation()->getZipCode(),
                    'city' => $contest->getOrganisation()->getCity(),
                ],
            ];

        return $this->json($item_json);

    }
}