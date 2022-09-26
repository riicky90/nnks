<?php

namespace App\Controller;

use App\Form\ExportType;
use App\Repository\RegistrationsRepository;
use Symfony\Component\HttpFoundation\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class ExportController extends AbstractController
{
    #[Route('/exportRegistrations/{contest}', name: 'export_registrations')]
    public function exportRegistrations(Request $request, RegistrationsRepository $registrationsRepository, $contest)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //set headers for excel file
        $sheet->setCellValue('A1', '#');
        $sheet->setCellValue('B1', 'Wedstrijd');
        $sheet->setCellValue('C1', 'Datum');
        $sheet->setCellValue('D1', 'Organisatie');
        $sheet->setCellValue('E1', 'Team');
        $sheet->setCellValue('F1', 'Trainer naam');
        $sheet->setCellValue('G1', 'Trainer e-mail');
        $sheet->setCellValue('H1', 'Dansschool');
        $sheet->setCellValue('I1', 'Aantal dansers');
        $sheet->setCellValue('J1', 'Dansers');

        $registrations = $registrationsRepository->findBy(['Contest' => $contest]);

        $count = 2;
        foreach ($registrations as $item) {
            $sheet->setCellValue('A' . $count, $item->getId());
            $sheet->setCellValue('B' . $count, $item->getContest()->getName());
            $sheet->setCellValue('C' . $count, $item->getContest()->getDate()->format('d-m-Y'));
            $sheet->setCellValue('D' . $count, $item->getContest()->getOrganisation()->getName());
            $sheet->setCellValue('E' . $count, $item->getTeam()->getName());
            $sheet->setCellValue('F' . $count, $item->getTeam()->getTrainerName());
            $sheet->setCellValue('G' . $count, $item->getTeam()->getMailTrainer());
            $sheet->setCellValue('H' . $count, $item->getTeam()->getUser()->getDansSchool());
            $sheet->setCellValue('I' . $count, count($item->getDancers()));
            $dancers = '';
            foreach ($item->getDancers() as $dancer) {
                $dancers .= $dancer->getFirstName() . ' ' . $dancer->getLastName() . ' ' . $dancer->getBirthDay()->format('d-m-Y') . ', ';
            }
            $sheet->setCellValue('I' . $count, $dancers);
            $count++;
        }

        $writer = new Xls($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="registraties-'.$registrations[0]->getContest()->getName().'.xls"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    #[Route('/exportform', name: 'export_form', methods: ['GET', 'POST'])]
    public function exportForm(Request $request)
    {
        $form = $this->createForm(ExportType::class, null, [
            'method' => 'POST',
            'action' => $this->generateUrl('export_form'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contest = $form->get('Contest')->getData()->getId();
            return $this->redirect($this->generateUrl('export_registrations', ['contest' => $contest]));
        }

        return $this->renderForm('registrations/_export.html.twig', [
            'form' => $form,
        ]);
    }
}