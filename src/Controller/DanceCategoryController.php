<?php

namespace App\Controller;

use App\Entity\DanceCategory;
use App\Form\DanceCategoryType;
use App\Repository\DanceCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dance/category')]
class DanceCategoryController extends AbstractController
{
    #[Route('/', name: 'dance_category_index', methods: ['GET'])]
    public function index(Request $request, DanceCategoryRepository $danceCategoryRepository, PaginatorInterface $paginator): Response
    {
        $filter = $request->query->get('filter');
        $reload = $request->query->get('reload');
        $template = "dance_category/index.html.twig";

        $danceCategories = $danceCategoryRepository->search($filter);

        $pagination = $paginator->paginate(
            $danceCategories,
            $request->query->getInt('page', 1),
            12
        );

        if ($reload) {
            $template = "dance_category/_list.html.twig";
        }

        return $this->render($template, [
            'dance_categories' => $pagination,
            'filter' => $filter
        ]);
    }

    #[Route('/new', name: 'dance_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $danceCategory = new DanceCategory();
        $form = $this->createForm(DanceCategoryType::class, $danceCategory,
            [
                'action' => $this->generateUrl('dance_category_new'),
                'method' => 'POST',
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($danceCategory);
            $entityManager->flush();

            $this->addFlash('success', 'Categorie opgeslagen.');

            return $this->redirectToRoute('dance_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dance_category/_form.html.twig', [
            'category' => $danceCategory,
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
    public function edit(Request $request, DanceCategory $danceCategory, EntityManagerInterface $entityManager, $id): Response
    {
        $form = $this->createForm(DanceCategoryType::class, $danceCategory,
            [
                'action' => $this->generateUrl('dance_category_edit', ['id' => $id]),
                'method' => 'POST',
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();
            $this->addFlash('success', 'Categorie opgeslagen.');
            return $this->redirect($request->headers->get('referer'));

        }

        return $this->renderForm('registrations/_form.html.twig', [
            'category' => $danceCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'dance_category_delete', methods: ['POST'])]
    public function delete(Request $request, DanceCategory $danceCategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $danceCategory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($danceCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dance_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
