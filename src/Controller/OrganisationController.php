<?php

namespace App\Controller;

use App\Entity\Organisation;
use App\Form\OrganisationType;
use App\Repository\OrganisationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/organisation')]
class OrganisationController extends AbstractController
{

    #[Route('/', name: 'organisation_index', methods: ['GET'])]
    public function index(Request $request, OrganisationRepository $organisationRepository, PaginatorInterface $paginator): Response
    {
        $filter = $request->query->get('filter');
        $reload = $request->query->get('reload');
        $template = "organisation/index.html.twig";

        $organisations = $organisationRepository->search($filter);

        $pagination = $paginator->paginate(
            $organisations,
            $request->query->getInt('page', 1),
            12
        );

        if ($reload) {
            $template = "organisation/_list.html.twig";
        }

        return $this->render($template, [
            'organisations' => $pagination,
            'filter' => $filter
        ]);
    }

    #[Route('/new', name: 'organisation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $organisation = new Organisation();
        $form = $this->createForm(OrganisationType::class, $organisation, [
            'action' => $this->generateUrl('organisation_new')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($organisation);
            $entityManager->flush();

            return new Response(null, 204);
        }

        return $this->renderForm('organisation/_form.html.twig', [
            'organisation' => $organisation,
            'form' => $form,
        ], new Response(null, $form->isSubmitted() ? 422 : 200));
    }

    #[Route('/{id}', name: 'organisation_show', methods: ['GET'])]
    public function show(Organisation $organisation): Response
    {
        return $this->render('organisation/show.html.twig', [
            'organisation' => $organisation,
        ]);
    }

    #[Route('/{id}/edit', name: 'organisation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, $id, Organisation $organisation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrganisationType::class, $organisation, [
            'action' => $this->generateUrl('organisation_edit', ['id' => $id]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($organisation);
            $entityManager->flush();

            return new Response(null, 204);
        }

        return $this->renderForm('organisation/_form.html.twig', [
            'organisation' => $organisation,
            'form' => $form,
        ], new Response(null, $form->isSubmitted() ? 422 : 200));
    }

    #[Route('/{id}', name: 'organisation_delete', methods: ['POST'])]
    public function delete(Request $request, Organisation $organisation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$organisation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($organisation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('organisation_index', [], Response::HTTP_SEE_OTHER);
    }
}
