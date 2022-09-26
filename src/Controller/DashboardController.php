<?php

namespace App\Controller;

use App\Repository\OrdersRepository;
use App\Repository\RegistrationsRepository;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard_index', methods: ['GET'])]
    public function index(ChartBuilderInterface $chartBuilder, OrdersRepository $ordersRepository, RegistrationsRepository $registrations, TeamRepository $teams)
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);

        $dates = array_reverse(array_column($registrations->chartDates(), 'formatted_date'));
        $count = array_reverse(array_column($registrations->chartDates(), 'count'));

        $chart->setData([
            'labels' => $dates,
            'datasets' => [
                [
                    'label' => 'Inschrijvingen',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $count,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 10,
                ],
            ],
        ]);

        $orders = $ordersRepository->findBy(["OrderStatus" => "payed"], ["createdAt" => "ASC"], 10, 0);

        return $this->render('dashboard/index.html.twig', [
            'chart' => $chart,
            'orders' => $orders,
            'totalTeams' => $teams->totalTeams(),
            'totalRegistrations' => $registrations->totalRegistrations()
        ]);
    }
}