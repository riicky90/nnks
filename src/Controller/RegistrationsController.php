<?php

namespace App\Controller;

use App\Entity\Registrations;
use App\Form\RegistrationsType;
use App\Repository\DancersRepository;
use App\Repository\OrdersRepository;
use App\Repository\OrganisationRepository;
use App\Repository\RegistrationsRepository;
use App\Service\ContestComplete;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/registrations')]
class RegistrationsController extends AbstractController
{
    #[Route('/', name: 'registrations_index', methods: ['GET'])]
    public function index(RegistrationsRepository $registrationsRepository, Request $request): Response
    {
        $filter = $request->query->get('filter');

        $registrations = $registrationsRepository->search($filter);

        return $this->render('registrations/index.html.twig', [
            'registrations' => $registrations,
            'filter' => $filter
        ]);
    }

    #[Route('/new', name: 'registrations_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $registration = new Registrations();
        $form = $this->createForm(RegistrationsType::class, $registration,
            [
                'action' => $this->generateUrl('registrations_new'),
                'method' => 'POST',
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $musicFile = $form->get('Music')->getData();

            if ($musicFile) {
                $musicFileName = $fileUploader->upload($musicFile);
                $registration->setMusicFile($musicFileName);
            }

            $entityManager->persist($registration);
            $entityManager->flush();

            return $this->redirectToRoute('registrations_index', [], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('registrations/_form.html.twig', [
            'registration' => $registration,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'registrations_show', methods: ['GET'])]
    public function show(Registrations $registration, RegistrationsRepository $registrationsRepository, $id, DancersRepository $dancersRepository, OrganisationRepository $organisation, OrdersRepository $orders): Response
    {
        return $this->render('registrations/show.html.twig', [
            'registration' => $registration,
            'totalDancers' => $registration->getDancers()->count() * 5.00,
            'totalOrder' => $orders->createQueryBuilder('o')
                ->select('SUM(o.Amount)')
                ->where('o.Registration = :registration')
                ->setParameter('registration', $registration)
                ->getQuery()
                ->getSingleScalarResult(),
        ]);
    }

    #[Route('/{id}/edit', name: 'registrations_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, $id, Registrations $registration, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(RegistrationsType::class, $registration,
            [
                'action' => $this->generateUrl('registrations_edit', ['id' => $id]),
                'method' => 'POST',
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $musicFile = $form->get('Music')->getData();

                if ($musicFile) {
                    $musicFileName = $fileUploader->upload($musicFile);
                    $registration->setMusicFile($musicFileName);
                }

                $entityManager->flush();

                $this->addFlash('success', 'Inschrijving opgeslagen.');

                return $this->redirect($request->headers->get('referer'));

            } catch (\Exception $e) {
                $this->addFlash('success', 'Inschrijving mislukt.<br /> ' . $e->getMessage());

            }
        }

        return $this->renderForm('registrations/_form.html.twig', [
            'registration' => $registration,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'registrations_delete', methods: ['POST'])]
    public function delete(Request $request, Registrations $registration, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $registration->getId(), $request->request->get('_token'))) {
            $entityManager->remove($registration);
            $entityManager->flush();
        }

        return $this->redirectToRoute('registrations_index', [], Response::HTTP_SEE_OTHER);
    }

}
