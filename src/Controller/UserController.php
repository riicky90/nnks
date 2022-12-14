<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

#[Route('/users')]
class UserController extends AbstractController
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        $filter = $request->query->get('filter');
        $reload = $request->query->get('reload');
        $template = "user/index.html.twig";

        $users = $userRepository->search($filter);

        $pagination = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            12
        );

        if ($reload) {
            $template = "user/_list.html.twig";
        }

        return $this->render($template, [
            'users' => $pagination,
            'filter' => $filter
        ]);
    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user,
            [
                'action' => $this->generateUrl('user_new'),
                'method' => 'POST',
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($password);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Gebruiker ' . $user->getEmail() . ' opgeslagen.');

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('user/_form.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, $id, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user,
            [
                'action' => $this->generateUrl('user_edit', ['id' => $id]),
                'method' => 'POST',
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('plainPassword')->getData() != null) {

                $password = $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                );
                $user->setPassword($password);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Gebruiker ' . $user->getEmail() . ' opgeslagen.');

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/_form.html.twig', [
            'id' => $id,
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Gebruiker ' . $user->getEmail() . ' verwijderd.');

        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/login-as/{id}', name: 'user_login_as', methods: ['GET'])]
    public function activate(Request $request, $id, EntityManagerInterface $entityManager, Session $session)
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        $token = new PostAuthenticationToken(
            $user,
            'main',
            $user->getRoles()
        );

        $this->tokenStorage->setToken($token);
        $session->set('_security_main', serialize($token));

        $this->addFlash('success', 'Je bent ingelogd als ' . $user->getEmail() . '.');

        return $this->redirectToRoute('fe_dashboard');
    }
}
