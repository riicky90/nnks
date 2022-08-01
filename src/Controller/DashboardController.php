<?php

namespace App\Controller;

use App\Repository\RegistrationsRepository;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard_index', methods: ['GET'])]
    public function index(ChartBuilderInterface $chartBuilder, RegistrationsRepository $registrations, TeamRepository $teams)
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

        return $this->render('dashboard/index.html.twig', [
            'chart' => $chart,
            'totalTeams' => $teams->totalTeams(),
            'totalRegistrations' => $registrations->totalRegistrations()
        ]);
    }
}