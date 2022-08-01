<?php

namespace App\Components;

use App\Entity\Registrations;
use App\Form\RegistrationsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

#[AsLiveComponent('registration_form')]
class RegistrationFormComponent extends AbstractController
{
    use ComponentWithFormTrait;

    #[LiveProp(fieldName: 'data')]
    public ?Registrations $registrations = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(RegistrationsType::class, $this->registrations);
    }
}