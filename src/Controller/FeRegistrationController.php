<?php

namespace App\Controller;

use App\Entity\Registrations;
use App\Entity\Team;
use App\Form\RegistrationsType;
use App\Repository\ContestRepository;
use App\Repository\OrdersRepository;
use App\Repository\RegistrationsRepository;
use App\Repository\TeamRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/{id}', name: 'fe_registrations_show', methods: ['GET'])]
    public function show(Registrations $registration, OrdersRepository $orders): Response
    {
        return $this->render('frontend/registrations/show.html.twig', [
            'registration' => $registration,
            'totalDancers' => $registration->getTeam()->getDancers()->count() * $registration->getContest()->getRegistrationFee(),
            'totalOrder' => $orders->createQueryBuilder('o')
                ->select('SUM(o.Amount)')
                ->andWhere('o.Registration = :registration')
                ->andWhere('o.OrderStatus = :paid')
                ->setParameter('registration', $registration)
                ->setParameter('paid', 'paid')
                ->getQuery()
                ->getSingleScalarResult(),
        ]);
    }

    #[Route('/register/{contest}', name: 'fe_registration_register')]
    public function register(Request $request, $contest, ContestRepository $contestRepository, FileUploader $fileUploader, EntityManagerInterface $entityManager, OrdersRepository $ordersRepository): Response
    {
        $contest = $contestRepository->find($contest);

        if ($contest->getRegistrationOpenFrom() >= new \DateTime()) {
            $this->addFlash('success', 'Registratie voor dit event is nog niet mogelijk');
            return $this->redirectToRoute('fe_contests_index');
        }

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
                    $musicFileName = $fileUploader->upload($musicFile, $registrations->getTeam());
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

            if ($request->request->get('save')) {
                $this->addFlash('success', 'Inschrijving opgeslagen.');
                return $this->redirectToRoute('fe_registrations_index');
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
                "edit-page" => true,
                "contest" => $curr->getContest(),
                'action' => $this->generateUrl('fe_registration_edit', ['registration' => $registration->getId()]),
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dancers = $form->get('Dancers')->getData();

            try {
                $musicFile = $form->get('Music')->getData();

                if ($musicFile) {
                    $musicFileName = $fileUploader->upload($musicFile, $registration->getTeam());
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

            if ($request->request->get('save')) {
                $this->addFlash('success', 'Inschrijving opgeslagen.');
                return $this->redirectToRoute('fe_registrations_index');
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

    //return number of dancer inside given team with route
    #[Route('/getdancers/{team}', name: 'fe_get_dancers')]
    public function getDancers(Team $team, TeamRepository $teamsRepository): Response
    {
        $team = $teamsRepository->find($team);
        $dancers = $team->getDancers();
        $count = count($dancers);


        //prepare dancers array with all details
        $dancersArray = [];
        foreach ($dancers as $dancer) {
            $dancersArray[] = [
                'id' => $dancer->getId(),
                'FirstName' => $dancer->getFirstName(),
                'SecondName' => $dancer->getSecondName(),
                'LastName' => $dancer->getLastName(),
                'AllDetails' => $dancer->getAllDetails(),
            ];
        }


        //combine count with dancers inside team return as json
        $response = new JsonResponse();
        $response->setData([
            'count' => $count,
            'dancers' => $dancersArray,
        ]);

        return $response;
    }
}