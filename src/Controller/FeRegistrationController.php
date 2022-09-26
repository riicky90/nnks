<?php

namespace App\Controller;

use App\Entity\Contest;
use App\Entity\Registrations;
use App\Form\ContestType;
use App\Form\RegistrationsType;
use App\Repository\ContestRepository;
use App\Repository\DancersRepository;
use App\Repository\OrdersRepository;
use App\Repository\RegistrationsRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
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
    public function registrations(RegistrationsRepository $registrationsRepository, Request $request, PaginatorInterface $paginator): Response
    {

        $registrations = $registrationsRepository->findBy(['CreatedBy' => $this->getUser()]);

        $pagination = $paginator->paginate(
            $registrations,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('frontend/registrations/index.html.twig', [
            'registrations' => $pagination,
        ]);
    }

    #[Route('/register/contest/{contest}', name: 'fe_registration_register')]
    public function register(Request $request, $contest, ContestRepository $contestRepository, FileUploader $fileUploader, EntityManagerInterface $entityManager, TeamRepository $teamRepository): Response
    {
        $contest = $contestRepository->find($contest);

        $registrations = new Registrations();
        $form = $this->createForm(RegistrationsType::class, $registrations,
            [
                'register' => true,
                'contest' => $contest,
                'action' => $this->generateUrl('fe_registration_register', ['contest' => $contest->getId()]),
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dancers = $form->get('Dancers')->getData();

            try {
                $musicFile = $form->get('Music')->getData();

                if ($musicFile) {
                    $musicFileName = $fileUploader->upload($musicFile);
                    $registrations->setMusicFile($musicFileName);
                }

            } catch (\Exception $e) {
                $this->addFlash('success', 'Inschrijving mislukt.<br /> ' . $e->getMessage());
            }

            $amount = count($dancers) * $contest->getRegistrationFee();

            $registrations->setContest($contest);
            $entityManager->persist($registrations);
            $entityManager->flush();

            return $this->redirectToRoute("make_payment",
                [
                    "registration" => $registrations->getId(),
                    "contest" => $contest->getId(),
                    "amount" => $amount
                ]);
        }

        return $this->render('frontend/registrations/new.html.twig', [
            'contest' => $contest,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/register/edit/{id}', name: 'fe_registration_edit')]
    public function edit(Request $request, Registrations $registration, EntityManagerInterface $entityManager, FileUploader $fileUploader, OrdersRepository $ordersRepository, ContestRepository $contestRepository): Response
    {
        $orders = $ordersRepository->findBy(['Registration' => $registration->getId(), 'OrderStatus' => 'payed']);

        $form = $this->createForm(RegistrationsType::class, $registration,
            [
                'register' => true,
                'edit' => false,
                'action' => $this->generateUrl('fe_registration_edit', ['id' => $registration->getId()]),
            ]);

        $contest = $contestRepository->find($registration->getContest()->getId());
        $team = $registration->getTeam();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dancers = $form->get('Dancers')->getData();

            $registration->setTeam($team);

            try {
                $musicFile = $form->get('Music')->getData();

                if ($musicFile) {
                    $musicFileName = $fileUploader->upload($musicFile);
                    $registration->setMusicFile($musicFileName);
                }

            } catch (\Exception $e) {
                $this->addFlash('success', 'Inschrijving mislukt.<br /> ' . $e->getMessage());
            }

            $amount = count($dancers) * $contest->getRegistrationFee();

            $registration->setContest($contest);
            $entityManager->persist($registration);
            $entityManager->flush();

            return $this->redirectToRoute("fe_registrations_index");
        }

        $total = 0;
        foreach ($orders as $order) {
            $total += $order->getAmount();
        }

        $form->remove('Team');

        return $this->renderForm('frontend/registrations/edit.html.twig', [
            'registration' => $registration,
            'Contest' => $contest,
            'form' => $form,
            'orderTotal' => $registration->getDancers()->count() * $registration->getContest()->getRegistrationFee(),
            'totalPayed' => $total,
        ]);
    }
}