<?php

namespace App\Components;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('random_number')]
class RandomNumberComponent extends AbstractController
{
    use DefaultActionTrait;

    public function getRandomNumber(): string
    {
        $value = $this->getUser()->getUserIdentifier(). rand(0,1000);

        return $value;
    }
}