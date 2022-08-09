<?php

namespace App\Controller;

use App\Entity\Registrations;
use App\Repository\DancersRepository;
use App\Repository\RegistrationsRepository;
use mPDF;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tickets')]
class TicketsController extends AbstractController
{
    #[Route('/download/{registration}', name: 'tickets_download', methods: ['GET'])]
    public function index($registration, RegistrationsRepository $registrationsRepository, DancersRepository $dancersRepository): Response
    {
        $registrations = $registrationsRepository->find($registration);

        $dancers = $dancersRepository->findBy([
            'team' => $registrations->getTeam()->getId()
        ]);

        $mpdf = new Mpdf();

        $mpdf->WriteHTML("test");

        $mpdf->Output('tickets.pdf', 'I');


    }

    #[Route('/email/{registration}', name: 'tickets_email', methods: ['GET'])]
    public function email($registration, RegistrationsRepository $registrationsRepository, DancersRepository $dancersRepository, MailerInterface $mailer): Response
    {
        $registrations = $registrationsRepository->find($registration);

        $dancers = $dancersRepository->findBy([
            'team' => $registrations->getTeam()->getId()
        ]);

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);

        $dompdf->loadHtml($this->renderView('/tickets/ticketlayout.html.twig', array(
            'registration' => $registrationsRepository->find($registration),
            'dancers' => $dancers
        )));

        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();

        $email = (new Email())
            ->from('info@nnks.nl')
            ->attach($output, 'Tickets '.$registrations->getTeam()->getName().' - '. $registrations->getContest()->getName().'.pdf')
            ->to($registrations->getTeam()->getMailTrainer())
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Tickets '.$registrations->getContest()->getName(). ' | ' .$registrations->getContest()->getDate()->format('d-m-Y').' NNKS.nl')
            ->html("Hallo,<p>Hierbij ontvang je de tickets voor ".$registrations->getContest()->getName() ." op ". $registrations->getContest()->getDate()->format('d-m-Y') );

        try {

            $mailer->send($email);
            $this->addFlash('success', 'E-mail is verzonden');

        } catch (TransportExceptionInterface $e) {
            $this->addFlash('error', 'E-mail is kon niet worden verstuurd');
        }

        return $this->redirectToRoute('registrations_show', ["id" => $registration]);

    }
}
