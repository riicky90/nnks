<?php

namespace App\Controller;

use App\Entity\Dancers;
use App\Form\DancersType;
use App\Repository\DancersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dancers')]
class DancersController extends AbstractController
{
    #[Route('/', name: 'dancers_index', methods: ['GET'])]
    public function index(DancersRepository $dancersRepository): Response
    {
        return $this->render('dancers/index.html.twig', [
            'dancers' => $dancersRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'dancers_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dancer = new Dancers();
        $form = $this->createForm(DancersType::class, $dancer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($dancer);
            $entityManager->flush();

            return $this->redirectToRoute('dancers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dancers/new.html.twig', [
            'dancer' => $dancer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'dancers_show', methods: ['GET'])]
    public function show(Dancers $dancer): Response
    {
        return $this->render('dancers/show.html.twig', [
            'dancer' => $dancer,
        ]);
    }

    #[Route('/{id}/edit', name: 'dancers_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dancers $dancer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DancersType::class, $dancer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('dancers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dancers/edit.html.twig', [
            'dancer' => $dancer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'dancers_delete', methods: ['POST'])]
    public function delete(Request $request, Dancers $dancer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dancer->getId(), $request->request->get('_token'))) {
            $entityManager->remove($dancer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dancers_index', [], Response::HTTP_SEE_OTHER);
    }
}
