<?php

namespace App\Controller;

use App\Repository\ContestRepository;
use App\Repository\OrdersRepository;
use App\Repository\RegistrationsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FeDashboardController extends AbstractController
{
    #[Route('/frontend/dashboard', name: 'fe_dashboard', methods: ['GET'])]
    public function index(OrdersRepository $ordersRepository, ContestRepository $contestRepository): Response
    {
        $orders = $ordersRepository->personalOrderLastTen($this->getUser());

        return $this->render('frontend/dashboard/index.html.twig', [
            'orders' => $orders,
            'totalTeams' => $this->getUser()->getTeams()->count(),
            'totalRegistrations' => $this->getUser()->getRegistrations()->count(),
            'totalContests' => count($contestRepository->allOpenContests()),
        ]);
    }
}