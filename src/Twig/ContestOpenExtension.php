<?php

namespace App\Twig;

use App\Entity\Contest;
use App\Repository\ContestRepository;
use App\Repository\OrdersRepository;
use App\Repository\RegistrationsRepository;
use Craue\ConfigBundle\Util\Config;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ContestOpenExtension extends AbstractExtension
{
    private $contestRepository;
    private $registrationRepository;
    private $orderRepository;
    private $config;

    public function __construct(ContestRepository $contestRepository, RegistrationsRepository $registrationsRepository, OrdersRepository $ordersRepository, Config $config)
    {
        $this->contestRepository = $contestRepository;
        $this->registrationRepository = $registrationsRepository;
        $this->orderRepository = $ordersRepository;
        $this->config = $config;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('contestOpen', [$this, 'contestOpen']),
            new TwigFunction('registrationpaid', [$this, 'registrationpaid'])
        ];
    }

    public function contestOpen(int $contest): bool
    {
        $contest = $this->contestRepository->find($contest);

        //check if registration date minus 5 days is before today
        $registrationDate = $contest->getDate();
        $registrationDate->modify('-'.$this->config->get('days_before_reg_close').' days');

        if ($registrationDate < new \DateTime()) {
            return false;
        }else{
            return true;
        }

    }

    public function registrationpaid(int $registration): bool
    {
        $registration = $this->registrationRepository->find($registration);
        $dancers = count($registration->getTeam()->getDancers());
        $totalDue = $dancers * $registration->getContest()->getRegistrationFee();

        //sum total orders that are paid for this contest
        $totalpaid = 0;
        foreach ($registration->getOrders() as $order) {
            if ($order->getOrderStatus() == "paid") {
                $totalpaid += $order->getAmount();
            }
        }

        $totalDue = number_format($totalDue, 2, '.', '');
        $totalpaid = number_format($totalpaid, 2, '.', '');

        if($totalpaid >= $totalDue){
            return false;
        }else {
            return true;
        }
    }
}