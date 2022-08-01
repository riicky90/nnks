<?php

namespace App\Controller;

use App\Entity\DanceCategory;
use App\Form\DanceCategoryType;
use App\Repository\DanceCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dance/category')]
class DanceCategoryController extends AbstractController
{
    #[Route('/', name: 'dance_category_index', methods: ['GET'])]
    public function index(DanceCategoryRepository $danceCategoryRepository): Response
    {
        return $this->render('dance_category/index.html.twig', [
            'dance_categories' => $danceCategoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'dance_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $danceCategory = new DanceCategory();
        $form = $this->createForm(DanceCategoryType::class, $danceCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($danceCategory);
            $entityManager->flush();

            return $this->redirectToRoute('dance_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dance_category/new.html.twig', [
            'dance_category' => $danceCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'dance_category_show', methods: ['GET'])]
    public function show(DanceCategory $danceCategory): Response
    {
        return $this->render('dance_category/show.html.twig', [
            'dance_category' => $danceCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'dance_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DanceCategory $danceCategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DanceCategoryType::class, $danceCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('dance_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dance_category/edit.html.twig', [
            'dance_category' => $danceCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'dance_category_delete', methods: ['POST'])]
    public function delete(Request $request, DanceCategory $danceCategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$danceCategory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($danceCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dance_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
