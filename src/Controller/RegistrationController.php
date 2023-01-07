<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\DependandTestType;
use App\Form\RegistrationFormType;
use App\Repository\DanceCategoryRepository;
use App\Repository\DancersRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('info@nnks.nl', 'NNKS Manager'))
                    ->to($user->getEmail())
                    ->subject('NNKS Bevestig je e-mailadres')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            $this->addFlash('success', 'Je account is aangemaakt, controleer je email voor de verificatie.');

            return $this->redirectToRoute('app_login');

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    //create test route and render depandend test form type
    #[Route('/test', name: 'app_test')]
    public function test(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        //render a form
        $form = $this->createForm(DependandTestType::class);
        $form->handleRequest($request);

        //if form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            //get data from form
            $data = $form->getData();
            //do something with data
            //...
            //redirect to homepage
        }

        //render form
        return $this->render('test/test.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    //create a route that returns the dancers related to a team
    #[Route('/get-dancers/json/{teamId}', name: 'app_get_dancers')]
    public function getDancers($teamId, DancersRepository $dancersRepository): Response
    {
        //get dancers from database
        $dancers = $dancersRepository->findBy(['team' => $teamId]);
        //create array to store dancer names
        $dancerNames = [];
        //loop through dancers
        foreach ($dancers as $dancer) {
            //add dancer name to array
            $dancerNames[] = [
                "value" => $dancer->getId(),
                    "text" => $dancer->getFullName()
            ];
        }

        $dancerNames = [
            "result" => $dancerNames
        ];

        //return dancer names as json
        return $this->json($dancerNames);
    }


    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->get('id');
        $user = $userRepository->find($id);

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));


            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Je e-mail adres is geverifieerd. Je kunt nu inloggen.');

        return $this->redirectToRoute('app_login');
    }
}
