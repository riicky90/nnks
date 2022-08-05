<?php

namespace App\Controller;

use App\Entity\Contest;
use App\Entity\Team;
use App\Form\ContestType;
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

#[Route('/frontend/team')]
class FeTeamController extends AbstractController
{

    #[Route('/', name: 'fe_team_index')]
    public function index(TeamRepository $teamRepository, Request $request, PaginatorInterface $paginator, UserRepository $userRepository, ContestRepository $contestRepository, DancersRepository $dancersRepository, OrganisationRepository $organisationRepository, RegistrationsRepository $registrationsRepository)
    {
        $userOrganisation = $userRepository->find($this->getUser())->getOrganisation();
        $q = $request->query->get('q');

        $teams = $teamRepository->searchPersonalTeam($userOrganisation, $q);

        $pagination = $paginator->paginate(
            $teams,
            $request->query->getInt('page', 1),
            10
        );

        $contests = $contestRepository->allOpenContests();

        return $this->render('frontend/team/index.html.twig', [
            "teams" => $pagination,
            "contests" => $contests,
            "organisation" => $userOrganisation,
            "teams_count" => $teamRepository->totalPersonalTeams($userOrganisation),
            "registrations_count" => $registrationsRepository->totalPersonalRegistrations($userOrganisation),
            "personal_registrations" => $registrationsRepository->personalRegistrations($userOrganisation)
        ]);
    }

    #[Route('/new', name: 'fe_team_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team, ['show_organisation' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $team->setOrganisation($this->getUser()->getOrganisation());
            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('fe_team_index');
        }

        return $this->renderForm('frontend/team/new.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'fe_team_delete', methods: ['POST'])]
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