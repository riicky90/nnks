<?php

namespace App\Controller;

use App\Repository\RegistrationsRepository;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tickets')]
class TicketsController extends AbstractController
{
    #[Route('/download/{registration}', name: 'tickets_download', methods: ['GET'])]
    public function index($registration, RegistrationsRepository $registrationsRepository, Pdf $pdf)
    {
        $registrations = $registrationsRepository->find($registration);
        $dancers = $registrations->getTeam()->getDancers();

        //check if amount paid is equal or higher than the registration fee
        $totalAmountPaid = 0;
        foreach ($registrations->getOrders() as $order) {
            if ($order->getOrderStatus() == 'paid') {
                $totalAmountPaid += $order->getAmount();
            }
        }
        $grandTotal = $registrations->getContest()->getRegistrationFee() * $dancers->count();

        if (!$totalAmountPaid >= $grandTotal) {
            return new Response('Registratie nog niet betaald');
        }

        $html = $this->render('tickets/ticketlayout.html.twig', [
            'registration' => $registrations,
            'dancers' => $dancers
        ])->getContent();

        //generate pdf and download in the browser
        $filename = 'ticket_' . $registrations->getId() . '.pdf';

        return new Response(
            $pdf->getOutputFromHtml($html),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"'
            ]
        );
    }

    #[Route('/email/{registration}', name: 'tickets_email', methods: ['GET'])]
    public function email($registration, RegistrationsRepository $registrationsRepository, Pdf $pdf, MailerInterface $mailer): Response
    {
        $registrations = $registrationsRepository->find($registration);

        //prepare pdf for email
        $dancers = $registrations->getTeam()->getDancers();
        $html = $this->render('tickets/ticketlayout.html.twig', [
            'registration' => $registrations,
            'dancers' => $dancers
        ])->getContent();
        $filename = 'ticket_' . $registrations->getId() . '.pdf';
        $pdf = $pdf->getOutputFromHtml($html);

        $email = (new Email())
            ->from('info@nnks.nl')
            ->attach($pdf, 'Tickets ' . $registrations->getTeam()->getName() . ' - ' . $registrations->getContest()->getName() . '.pdf')
            ->to($registrations->getTeam()->getMailTrainer())
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Tickets ' . $registrations->getContest()->getName() . ' | ' . $registrations->getContest()->getDate()->format('d-m-Y') . ' NNKS.nl')
            ->html("Hallo,<p>Hierbij ontvang je de tickets voor " . $registrations->getContest()->getName() . " op " . $registrations->getContest()->getDate()->format('d-m-Y'));

        try {

            $mailer->send($email);
            $this->addFlash('success', 'E-mail is verzonden');

        } catch (TransportExceptionInterface $e) {
            $this->addFlash('error', 'E-mail is kon niet worden verstuurd');
        }

        return $this->redirectToRoute('registrations_show', ["id" => $registration]);

    }
}
