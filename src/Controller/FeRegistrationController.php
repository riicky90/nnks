<?php

namespace App\Controller;

use App\Entity\Registrations;
use App\Form\ContestType;
use App\Form\RegistrationsType;
use App\Repository\ContestRepository;
use App\Repository\DancersRepository;
use App\Repository\RegistrationsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/frontend/registration')]
class FeRegistrationController extends AbstractController
{
    #[Route('/', name: 'fe_registrations_index')]
    public function registrations(RegistrationsRepository $registrationsRepository, Request $request, PaginatorInterface $paginator, UserRepository $userRepository, DancersRepository $dancersRepository): Response
    {
        $userOrganisation = $userRepository->find($this->getUser())->getOrganisation();

        $registrations = $registrationsRepository->personalRegistrations($userOrganisation);

        $pagination = $paginator->paginate(
            $registrations,
            $request->query->getInt('page', 1),
            10
        );


        return $this->render('frontend/registrations/index.html.twig', [
            'registrations' => $pagination,
        ]);
    }

    #[Route('/register', name: 'fe_registration_register')]
    public function register(Request $request, ContestRepository $contestRepository, EntityManagerInterface $entityManager): Response
    {
        $contest = $contestRepository->find($request->get('contest'));

        $registrations = new Registrations();
        $form = $this->createForm(RegistrationsType::class, $registrations,
            [
                'register' => true
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registrations->setContest($contest);
            $entityManager->persist($registrations);
            $entityManager->flush();

            return $this->forward('App\Controller\PaymentController::makePayment', [
                'method' => 'ideal',
                'amount' => 0.50,
                'orderid' => $registrations->getId(),
                'registration' => $registrations
            ]);
        }

        return $this->render('frontend/registrations/new.html.twig', [
            'registrations' => $registrations,
            'form' => $form->createView(),
        ]);
    }
}