<?php

namespace App\EventListener;

use App\Entity\Orders;
use App\Entity\Registrations;
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

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Orders) {
            return;
        }

        if($entity->getOrderStatus() == 'paid') {

            $email = (new Email())
                ->from('info@nnks.nl')
                ->to($entity->getRegistration()->getTeam()->getUser()->getEmail())
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Nieuwe betaling via NNKS manager')
                ->html('Nieuwe betaling ontvangen voor team ' . $entity->getRegistration()->getTeam()->getName() . ' Bedrag ontvangen: €'. $entity->getAmount());

            $this->mailer->send($email);
        }

    }
}
