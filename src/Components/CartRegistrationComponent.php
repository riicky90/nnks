<?php

namespace App\Components;

use App\Repository\OrganisationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('cart_registration')]
class CartRegistrationComponent extends AbstractController
{
    public string $type = 'success';
    public string $message;
    public OrganisationRepository $organisationRepository;

    public function __construct(OrganisationRepository $organisationRepository)
    {
        $this->organisationRepository = $organisationRepository;
    }

    public function getCart():array
    {

        return $this->getUser();
    }

}