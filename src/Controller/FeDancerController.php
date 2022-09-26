<?php

namespace App\Controller;

use App\Entity\Contest;
use App\Entity\Dancers;
use App\Entity\Team;
use App\Form\ContestType;
use App\Form\DancersType;
use App\Form\TeamType;
use App\Repository\ContestRepository;
use App\Repository\DancersRepository;
use App\Repository\OrganisationRepository;
use App\Repository\RegistrationsRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/frontend/dancers')]
class FeDancerController extends AbstractController
{

    #[Route('/', name: 'fe_dancers_index')]
    public function dancers(RegistrationsRepository $registrationsRepository, UserRepository $userRepository, DancersRepository $dancersRepository): Response
    {
        $userOrganisation = $userRepository->find($this->getUser())->getOrganisation();

        $dancers = $dancersRepository->findByUser($this->getUser());

        return $this->render('frontend/dancers/index.html.twig', [
            'dancers' => $dancers,
        ]);
    }

    #[Route('/new', name: 'fe_dancer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dancer = new Dancers();
        $form = $this->createForm(DancersType::class, $dancer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($dancer);
            $entityManager->flush();

            return $this->redirectToRoute('fe_dancers_index');
        }

        $form->remove('registrations');

        return $this->renderForm('frontend/dancers/new.html.twig', [
            'dancers' => $dancer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'fe_dancer_delete', methods: ['POST'])]
    public function delete(Request $request, Team $team, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $entityManager->remove($team);
            $entityManager->flush();
        }

        return $this->redirectToRoute('fe_team_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/edit', name: 'fe_team_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Team $team, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TeamType::class, $team, [
            'show_organisation' => false
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('fe_team_index');
        }

        return $this->renderForm('/frontend/team/edit.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }
}