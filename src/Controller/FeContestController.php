<?php

namespace App\Controller;

use App\Entity\Contest;
use App\Form\ContestType;
use App\Repository\ContestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/frontend/contests')]
class FeContestController extends AbstractController
{
    #[Route('/', name: 'fe_contests_index')]
    public function index(ContestRepository $contestRepository): Response
    {
        $contests = $contestRepository->allOpenContests();

        return $this->render('frontend/contests/index.html.twig', [
            'contests' => $contests,
        ]);
    }

    #[Route('/new', name: 'fe_contest_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contest = new Contest();
        $form = $this->createForm(ContestType::class, $contest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contest);
            $entityManager->flush();

            return $this->redirectToRoute('fe_contests_index');
        }

        return $this->renderForm('frontend/contests/new.html.twig', [
            'contest' => $contest,
            'form' => $form,
        ]);
    }
}