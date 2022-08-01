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
    public function orders(ContestRepository $contestRepository, UserRepository $userRepository, OrdersRepository $ordersRepository): Response
    {
        $userOrganisation = $userRepository->find($this->getUser())->getOrganisation();
        $orders = $ordersRepository->personalOrder($userOrganisation);

        return $this->render('frontend/orders/index.html.twig', [
            'orders' => $orders,
        ]);
    }
}
