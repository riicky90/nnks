<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\Organisation;
use App\Repository\ContestRepository;
use App\Repository\OrdersRepository;
use App\Repository\OrganisationRepository;
use App\Repository\RegistrationsRepository;
use Craue\ConfigBundle\Util\Config;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class PaymentController extends AbstractController
{
    private $mollie;

    public function __construct()
    {
        $this->mollie = new \Mollie\Api\MollieApiClient;
    }

    #[Route('/frontend/paymentmethods')]
    public function listPaymentOptions()
    {
        $payments = $this->mollie->payments->page();

        dd($payments);
    }

    #[Route('/frontend/makepayment/{registration}/{amount}', name: "make_payment", methods: ['GET'])]
    public function makePayment($registration, $amount, RegistrationsRepository $registrationsRepository, EntityManagerInterface $entityManager)
    {
        $registration = $registrationsRepository->find($registration);
        $amountFormatted = number_format($amount, 2, '.', ',');

        $this->mollie->setApiKey($registration->getContest()->getOrganisation()->getMollieApiKey());


        $payment = $this->mollie->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => $amountFormatted
            ],
            "description" => "Order #1234",
            "redirectUrl" => $this->generateUrl('payment_result', ["orderid" => "1234"], UrlGeneratorInterface::ABSOLUTE_URL),
            "webhookUrl" => ""
        ]);

        $order = new Orders();

        $order->setOrderStatus("pending");
        $order->setOrderNumber("1234");
        $order->setAmount($amountFormatted);
        $order->setRegistration($registrationsRepository->find($registration));
        $entityManager->persist($order);
        $entityManager->flush();

        return $this->redirect($payment->getCheckoutUrl());

    }

    #[Route('frontend/payment/result/{orderid}', name: "payment_result")]
    public function paymentResult($orderid, OrdersRepository $ordersRepository, EntityManagerInterface $entityManager)
    {
        try {
            $this->mollie->setApiKey($this->getUser()->getOrganisation()->getMollieApiKey());

            $payment = $this->mollie->orders->get($orderid);

            if ($payment->status == "completed") {
                $order = $ordersRepository->findOneBy(['OrderNumber' => $orderid]);
                $order->setOrderStatus("payed");
                $entityManager->flush();
            } else {
                $order = $ordersRepository->findOneBy(['OrderNumber' => $orderid]);
                $order->setOrderStatus("failed");
                $entityManager->flush();
            }

            return $this->render('frontend/orders/status.html.twig', [
                'payment' => $payment,
            ]);
        } catch (\Exception $e) {

            $error = [
                "status" => "error",
                "message" => $e->getMessage()
            ];

            return $this->render('frontend/orders/status.html.twig', [
                'payment' => $error,
            ]);
        }


    }

    #[Route('/createpaymentlink', methods: ['POST'], name: "create_payment_link")]
    public function createPaymentLink(Request $request, RegistrationsRepository $registrationsRepository, MailerInterface $mailer)
    {

        $method = $request->get('method');
        $amount = $request->get('amount');
        $orderID = $request->get('orderid');
        $registration = $request->get('registration');

        $currRegistration = $registrationsRepository->find($registration);

        $this->mollie->setApiKey($currRegistration->getTeam()->getOrganisation()->getMollieApiKey());

        $payment = $this->mollie->paymentLinks->create([
            "amount" => [
                "currency" => "EUR",
                "value" => number_format($amount, 2, '.', '')
            ],
            "description" => "Order #{$orderID}",
            "webhookUrl" => "http://alphaproducties.nl/"

        ]);

        $email = (new Email())
            ->from('info@nnks.nl')
            ->to($currRegistration->getTeam()->getMailTrainer())
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject("Betaal verzoek inschrijving voor {$currRegistration->getContest()->getName()} op NNKS.nl")
            ->html("Hallo {$currRegistration->getTeam()->getTrainerName()},<p>Hierbij ontvang je een betaal verzoek voor de inschrijving {$currRegistration->getContest()->getName()} met team {$currRegistration->getTeam()->getName()} </p><p><a href='{$payment->getCheckoutUrl()}'>Betalen</a></p>");

        try {

            $mailer->send($email);
            $this->addFlash("success", "E-mail met betaallink is verzonden naar: {$currRegistration->getTeam()->getMailTrainer()} ");

        } catch (TransportExceptionInterface $e) {
            $this->addFlash('error', 'E-mail is kon niet worden verstuurd');
        }

        return $this->redirect($request->headers->get('referer'));

    }

}