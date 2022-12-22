<?php

namespace App\Form;

use App\Entity\Dancers;
use App\Entity\Team;
use App\Repository\TeamRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DancersSelectForm extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $teams = $this->security->getUser()->getTeams();

        //create an array with team as key and the related trainers as value
        $teamsWithTrainers = [];
        foreach ($teams as $team) {
            $teamsWithTrainers[$team->getName()] = $team->getTrainer();
        }

        $builder->add('team', ChoiceType::class, [
            'choices' => $teamsWithTrainers,
            'label' => 'Team',
            'placeholder' => 'Selecteer een team',
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();

                $dancers = $data?->getTrainer();

                $this->addDancersField($event->getForm(), $dancers);
            });
    }


    public function addDancersField(FormInterface $form, ?Dancers $dancers)
    {
        $choices = null === $dancers ? [] : $dancers;

        $form->add('Dancers', ChoiceType::class, [
            'placeholder' => null === $dancers ? 'Selecteer eerst een team' : 'blaat',
            'choices' => $choices,
            'invalid_message' => false,
        ]);
    }


}