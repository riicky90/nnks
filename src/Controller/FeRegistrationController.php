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
use Craue\ConfigBundle\Util\Config;
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
        $filter = $request->query->get('filter');

        $registrations = $registrationsRepository->searchPersonal($filter, $this->getUser());

        $pagination = $paginator->paginate(
            $registrations,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('frontend/registrations/index.html.twig', [
            'registrations' => $pagination,
            'filter' => $filter,
        ]);
    }

    #[Route('/register/{contest}', name: 'fe_registration_register')]
    public function register(Request $request, $contest, ContestRepository $contestRepository, FileUploader $fileUploader, EntityManagerInterface $entityManager, OrdersRepository $ordersRepository): Response
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

            $registrations->setContest($contest);
            $entityManager->persist($registrations);
            $entityManager->flush();

            $amount = count($dancers) * $contest->getRegistrationFee();

            $orders = $ordersRepository->findBy(['Registration' => $registrations]);

            $amountpaid = 0;
            foreach ($orders as $order) {
                $amountpaid += $order->getAmount();
            }

            if ($amountpaid < $amount) {
                return $this->redirectToRoute("make_payment",
                    [
                        "registration" => $registrations->getId(),
                        "contest" => $contest->getId(),
                        "amount" => $amount
                    ]);
            } else {
                $this->addFlash('success', 'Inschrijving opgeslagen.');
                return $this->redirectToRoute("fe_contests_index");
            }


        }
        return $this->render('frontend/registrations/new.html.twig', [
            'contest' => $contest,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{registration}', name: 'fe_registration_edit')]
    public function edit(Request $request, Registrations $registration, RegistrationsRepository $registrationsRepository, EntityManagerInterface $entityManager, FileUploader $fileUploader, OrdersRepository $ordersRepository): Response
    {
        $curr = $registrationsRepository->find($registration->getId());

        $form = $this->createForm(RegistrationsType::class, $registration,
            [
                'register' => true,
                'action' => $this->generateUrl('fe_registration_edit', ['registration' => $registration->getId()]),
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dancers = $form->get('Dancers')->getData();

            try {
                $musicFile = $form->get('Music')->getData();

                if ($musicFile) {
                    $musicFileName = $fileUploader->upload($musicFile);
                    $registration->setMusicFile($musicFileName);
                }

            } catch (\Exception $e) {
                $this->addFlash('success', 'Inschrijving mislukt.<br /> ' . $e->getMessage());
            }

            $registration->setContest($curr->getContest());
            $entityManager->persist($registration);
            $entityManager->flush();

            $amount = count($dancers) * $curr->getContest()->getRegistrationFee();
            $orders = $ordersRepository->findBy(['Registration' => $curr->getId(), 'OrderStatus' => 'paid']);

            $amountpaid = 0;
            foreach ($orders as $order) {
                $amountpaid += $order->getAmount();
            }


            if ($amountpaid < $amount) {
                return $this->redirectToRoute("make_payment",
                    [
                        "registration" => $registration->getId(),
                        "contest" => $curr->getContest()->getId(),
                    ]);
            } else {
                $this->addFlash('success', 'Inschrijving opgeslagen.');
                return $this->redirectToRoute("fe_registrations_index");
            }

        }
        $orders = $ordersRepository->findBy(['Registration' => $curr->getId(), 'OrderStatus' => 'paid']);

        $amountpaid = 0;
        foreach ($orders as $order) {
            $amountpaid += $order->getAmount();
        }
        return $this->render('frontend/registrations/edit.html.twig', [
            'registration' => $registration,
            'totalpaid' => $amountpaid,
            'form' => $form->createView(),
        ]);
    }
}