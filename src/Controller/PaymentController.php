<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Repository\OrdersRepository;
use App\Repository\RegistrationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mollie\Api\MollieApiClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    private MollieApiClient $mollie;

    public function __construct()
    {
        $this->mollie = new MollieApiClient;
    }

    #[Route('/frontend/makepayment/{registration}', name: "make_payment", methods: ['GET'])]
    public function makePayment($registration, RegistrationsRepository $registrationsRepository, OrdersRepository $ordersRepository, EntityManagerInterface $entityManager)
    {
        $registration = $registrationsRepository->find($registration);

        $paidOrders = $ordersRepository->findBy(['Registration' => $registration, 'OrderStatus' => 'paid']);
        $paidAmount = 0;
        foreach ($paidOrders as $paidOrder) {
            $paidAmount += $paidOrder->getAmount();
        }

        $dancers = $registration->getTeam()->getDancers()->count();
        $amount = $dancers * $registration->getContest()->getRegistrationFee() - $paidAmount;
        $amount = number_format($amount, 2, '.', '');

        $this->mollie->setApiKey($registration->getContest()->getOrganisation()->getMollieApiKey());

        $orderId = time().mt_rand();

        $payment = $this->mollie->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => $amount
            ],
            "description" => "Order #" . $orderId . ". Wedstrijd: " . $registration->getContest()->getName(),
            "redirectUrl" => $this->generateUrl('payment_result', ["orderid" => $orderId], UrlGeneratorInterface::ABSOLUTE_URL),
            "metadata" => [
                "order_id" => "#" . $orderId,
                "team" => $registration->getTeam()->getName(),
            ],
            "webhookUrl" => $this->generateUrl('mollie_webhook', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        $order = new Orders();
        $order->setOrderStatus($payment->status);
        $order->setOrderNumber($payment->id);
        $order->setOrderId($orderId);
        $order->setAmount($amount);
        $order->setRegistration($registrationsRepository->find($registration));
        $entityManager->persist($order);
        $entityManager->flush();

        return $this->redirect($payment->getCheckoutUrl());

    }

    #[Route('frontend/payment/result/{orderid}', name: "payment_result")]
    public function paymentResult($orderid, OrdersRepository $ordersRepository, TicketsController $ticketsController, EntityManagerInterface $entityManager): Response
    {
        $order = $ordersRepository->findOneBy(["OrderId" => $orderid]);

        $this->mollie->setApiKey($order->getRegistration()->getContest()->getOrganisation()->getMollieApiKey());

        if(str_starts_with($order->getOrderNumber(), 'pl_'))
        {
            $payment = $this->mollie->paymentLinks->get($order->getOrderNumber());
        }else{
            $payment = $this->mollie->payments->get($order->getOrderNumber());
        }



        if($payment->isPaid())
        {
            $order->setOrderStatus("paid");
            $entityManager->persist($order);
            $entityManager->flush();
        }


        return $this->render('frontend/orders/status.html.twig', [
            'payment' => $payment,
            'order' => $order
        ]);

    }

    #[Route('/createpaymentlink/{registration}', name: "create_payment_link", methods: ['GET'])]
    public function createPaymentLink(Request $request, $registration, EntityManagerInterface $entityManager, OrdersRepository $ordersRepository, RegistrationsRepository $registrationsRepository, MailerInterface $mailer)
    {
        $registration = $registrationsRepository->find($registration);

        $paidOrders = $ordersRepository->findBy(['Registration' => $registration, 'OrderStatus' => 'paid']);
        $paidAmount = 0;
        foreach ($paidOrders as $paidOrder) {
            $paidAmount += $paidOrder->getAmount();
        }

        $dancers = $registration->getTeam()->getDancers()->count();
        $amount = $dancers * $registration->getContest()->getRegistrationFee() - $paidAmount;
        $amount = number_format($amount, 2, '.', '');

        $this->mollie->setApiKey($registration->getContest()->getOrganisation()->getMollieApiKey());

        $orderId = time().mt_rand();

        $payment = $this->mollie->paymentLinks->create([
            "amount" => [
                "currency" => "EUR",
                "value" => $amount
            ],
            "description" => "Order #" . $orderId . ". Wedstrijd: " . $registration->getContest()->getName(),
            "redirectUrl" => $this->generateUrl('payment_result', ["orderid" => $orderId], UrlGeneratorInterface::ABSOLUTE_URL),
            "webhookUrl" => $this->generateUrl('mollie_webhook', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        $order = new Orders();
        $order->setOrderStatus('open');
        $order->setOrderNumber($payment->id);
        $order->setOrderId($orderId);
        $order->setAmount($amount);
        $order->setRegistration($registrationsRepository->find($registration));
        $entityManager->persist($order);
        $entityManager->flush();

        $email = (new Email())
            ->from('info@nnks.nl')
            ->to($registration->getTeam()->getMailTrainer())
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject("Betaal verzoek inschrijving voor {$registration->getContest()->getName()} op NNKS.nl")
            ->html("Hallo {$registration->getTeam()->getTrainerName()},<p>Hierbij ontvang je een betaal verzoek voor de inschrijving {$registration->getContest()->getName()} met team {$registration->getTeam()->getName()} </p><p><a href='{$payment->getCheckoutUrl()}'>Betalen</a></p>");

        try {

            $mailer->send($email);
            $this->addFlash("success", "E-mail met betaallink is verzonden naar: {$registration->getTeam()->getMailTrainer()} ");

        } catch (TransportExceptionInterface $e) {
            $this->addFlash('error', 'E-mail is kon niet worden verstuurd');
        }

        return $this->redirect($request->headers->get('referer'));

    }

    #[Route('/mollie-webhook', name: "mollie_webhook")]
    public function webhook(Request $request, OrdersRepository $ordersRepository, EntityManagerInterface $entityManager)
    {
        $paymentID = $request->get('id');

        $order = $ordersRepository->findOneBy(["OrderNumber" => $paymentID]);
        $organisation = $order->getRegistration()->getContest()->getOrganisation();

        $this->mollie->setApiKey($organisation->getMollieApiKey());

        $payment = $this->mollie->payments->get($request->get('id'));

        if($payment->isPaid())
        {
            $order->setOrderStatus("paid");
            $entityManager->persist($order);
            $entityManager->flush();
        }

        return new Response("OK", 200);
    }
}