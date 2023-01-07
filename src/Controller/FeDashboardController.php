<?php

namespace App\Controller;

use App\Form\DansschoolPopupType;
use App\Form\TeamType;
use App\Repository\ContestRepository;
use App\Repository\OrdersRepository;
use App\Repository\RegistrationsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/frontend/dashboard/dansschoolpopup', name: 'fe_dashboard_dansschoolpopup', methods: ['POST', 'GET'])]
    public function dansschoolpopup(Request $request,EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $form = $this->createForm(DansschoolPopupType::class, [
            'action' => $this->generateUrl('fe_dashboard_dansschoolpopup')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $currentUser = $userRepository->find($this->getUser());
            $currentUser->setDansschool($form->get('DansSchoolNaam')->getData());

            $entityManager->flush();

            $this->addFlash('success', 'Dansschool naam opgeslagen');
            return new Response(null, 204);


        }

        return $this->render('/frontend/team/_form.html.twig', [
            'form' => $form,
        ]);
    }
}