<?php


namespace App\EventListener;

use App\Entity\Registrations;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Gedmo\Loggable\Entity\LogEntry;
use Symfony\Component\Security\Core\Security;

class RegistrationEventListener
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {

    }

    public function postPersist(LifecycleEventArgs $args): void
    {


    }


}