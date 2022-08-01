<?php

namespace App\Components;

use App\Entity\Registrations;
use App\Repository\RegistrationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('registration_table')]
class RegistrationTableComponent extends AbstractController
{
    use DefaultActionTrait;

    private $registrationRepo;
    public function __construct(RegistrationsRepository $registrationsRepository)
    {
        $this->registrationRepo = $registrationsRepository;
    }

    public function getregistrations(): array
    {
        dd($this->registrationRepo->findAll());
        return ["registrations" => $this->registrationRepo->findAll()] ;

    }
}