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
        $entity = $args->getObject();

        if(!$args->getObject() instanceof Registrations) {
            return;
        }

        $em = $args->getObjectManager();

        // Create a new Gemdo log entry
        $logEntry = new LogEntry();
        $logEntry->setAction('update');
        $logEntry->setLoggedAt();
        $logEntry->setObjectId($entity->getId());
        $logEntry->setObjectClass($em->getClassMetadata(get_class($entity))->getName());
        $logEntry->setVersion(time());
        $logEntry->setData($entity->getDancers());
        $logEntry->setUsername($this->security->getUser()->getEmail());

        $em->persist($logEntry);
        $em->flush();

    }

    //pre persist
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if(!$args->getObject() instanceof Registrations) {
            return;
        }

        $em = $args->getObjectManager();

        // Create a new Gemdo log entry
        $logEntry = new LogEntry();
        $logEntry->setAction('create');
        $logEntry->setLoggedAt();
        $logEntry->setObjectId($entity->getId());
        $logEntry->setObjectClass($em->getClassMetadata(get_class($entity))->getName());
        $logEntry->setVersion(time());
        $logEntry->setData($entity->getDancers());
        $logEntry->setUsername($this->security->getUser()->getEmail());

        $em->persist($logEntry);
        $em->flush();
    }
}