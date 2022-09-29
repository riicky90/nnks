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
class FeOrdersController extends AbstractController
{

    #[Route('/orders', name: 'fe_orders_index')]
    public function orders(OrdersRepository $ordersRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $filter = $request->query->get('filter');

        $orders = $ordersRepository->personalOrderSearch($filter, $this->getUser());

        $paginator = $paginator->paginate(
            $orders,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('frontend/orders/index.html.twig', [
            'orders' => $paginator,
            'filter' => $filter,
        ]);
    }

    #[Route('/profile/edit', name: 'user_profile')]
    public function profile(): Response
    {
        return $this->render('/frontend/user/profile.html.twig');
    }
}
