<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserListener
{
    private $mailer;
    private $currUser;

    public function __construct(MailerInterface $mailer,  #[CurrentUser] ?User $user)
    {
        $this->mailer = $mailer;
        $this->currUser = $user;
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof User) {
            return;
        }

        $email = (new Email())
            ->from('info@nnks.nl')
            ->to($entity->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Nieuwe gebruiker bij NNKS.nl')
            ->html('<p>Je bent aangemaakt als nieuwe gebruiker:</p><br />'.$entity->getEmail());

        $this->mailer->send($email);

    }
}
