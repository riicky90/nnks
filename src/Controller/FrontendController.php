<?php

namespace App\Controller;

use App\Entity\Registrations;
use App\Entity\Team;
use App\Form\RegistrationsType;
use App\Form\TeamType;
use App\Repository\ContestRepository;
use App\Repository\DancersRepository;
use App\Repository\OrdersRepository;
use App\Repository\OrganisationRepository;
use App\Repository\RegistrationsRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/frontend')]
class FrontendController extends AbstractController
{

    #[Route('/orders', name: 'fe_orders_index')]
    public function orders(OrdersRepository $ordersRepository): Response
    {
        $orders = $ordersRepository->findAll();

        return $this->render('frontend/orders/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/profile/edit', name: 'user_profile')]
    public function profile(): Response
    {
        return $this->render('/frontend/user/profile.html.twig');
    }

    //return json formatted dancers list for autocomplete
    #[Route('/autocomplete', name: 'fe_dancers_autocomplete')]
    public function dancersAutocomplete(DancersRepository $dancersRepository): Response
    {
        $dancers = $dancersRepository->findAll();
        $dancersList = [];
        foreach ($dancers as $dancer) {
            $dancersList[] = [
                'value' => $dancer->getId(),
                'label' => $dancer->getFirstName() . ' ' . $dancer->getLastName(),
            ];
        }

        $res = array("results" => $dancersList);

        return $this->json($res);
    }
}
