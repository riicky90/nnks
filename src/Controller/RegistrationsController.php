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
use Craue\ConfigBundle\Util\Config;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/registrations')]
class RegistrationsController extends AbstractController
{
    #[Route('/', name: 'registrations_index', methods: ['GET'])]
    public function index(RegistrationsRepository $registrationsRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $filter = $request->query->get('filter');
        $reload = $request->query->get('reload');
        $template = "registrations/index.html.twig";

        $registrations = $registrationsRepository->search($filter);

        $pagination = $paginator->paginate(
            $registrations,
            $request->query->getInt('page', 1),
            12
        );

        if ($reload) {
            $template = "registrations/_list.html.twig";
        }

        return $this->render($template, [
            'registrations' => $pagination,
            'filter' => $filter
        ]);
    }

    #[Route('/new', name: 'registrations_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $registration = new Registrations();
        $form = $this->createForm(RegistrationsType::class, $registration, [
            'action' => $this->generateUrl('registrations_new')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $musicFile = $form->get('Music')->getData();

            if ($musicFile) {
                $fileName = $fileUploader->upload($musicFile);
                $registration->setMusicFile($fileName);
            }

            $entityManager->persist($registration);
            $entityManager->flush();

            return new Response(null, 204);
        }

        return $this->renderForm('registrations/_form.html.twig', [
            'registration' => $registration,
            'form' => $form,
        ], new Response(null, $form->isSubmitted() ? 422 : 200));
    }

    #[Route('/{id}', name: 'registrations_show', methods: ['GET'])]
    public function show(Registrations $registration, $id, OrdersRepository $orders, Config $config): Response
    {
        return $this->render('registrations/show.html.twig', [
            'registration' => $registration,
            'totalDancers' => $registration->getDancers()->count() * $registration->getContest()->getRegistrationFee(),
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

    #[Route('/{id}/edit', name: 'registrations_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, $id, Registrations $registration, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(RegistrationsType::class, $registration, [
            'action' => $this->generateUrl('registrations_edit', ['id' => $id]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $musicFile = $form->get('Music')->getData();

            if ($musicFile) {
                $fileName = $fileUploader->upload($musicFile);
                $registration->setMusicFile($fileName);
            }

            $entityManager->persist($registration);
            $entityManager->flush();

            return new Response(null, 204);
        }

        return $this->renderForm('registrations/_form.html.twig', [
            'registration' => $registration,
            'form' => $form,
        ], new Response(null, $form->isSubmitted() ? 422 : 200));
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

    #[Route('/{id}/removeMusic', name: 'registrations_removeMusic', methods: ['POST'])]
    public function removeMusic(Request $request, Registrations $registration, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $referer = $request->headers->get('referer');

        $fileUploader->removeFile($registration->getMusicFile());

        if ($this->isCsrfTokenValid('delete' . $registration->getId(), $request->request->get('_token'))) {
            $registration->setMusicFile("");
            $entityManager->persist($registration);
            $entityManager->flush();

            $this->addFlash('success', 'Muziek bestand is verwijderd');
        }else{
            $this->addFlash('error', 'Muziek bestand kon niet verwijderd worden');
        }



        return $this->redirect($referer);
    }

}
