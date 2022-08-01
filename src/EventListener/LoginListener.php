<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoginListener implements EventSubscriberInterface
{
    private $em;

    public function __construct (EntityManagerInterface $em){
        $this->em = $em;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess'
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        $user->setLastLogin(new \DateTime());

        $this->em->persist($user);
        $this->em->flush();
    }
}