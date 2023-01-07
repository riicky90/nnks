<?php

namespace App\Form;

use App\Entity\DanceCategory;
use App\Entity\Dancers;
use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name', null, [
                'label' => 'Naam',
                'attr' => [
                    'placeholder' => 'Naam van het team'
                ]
            ])
            ->add('TrainerName', null, [
                'label' => 'Voornaam coach',
                'attr' => [
                    'placeholder' => 'Naam van de coach'
                ]
            ])
            ->add('TrainerLastname', null, [
                'label' => 'Achternaam coach',
                'attr' => [
                    'placeholder' => 'Achternaam van de coach'
                ]
            ])
            ->add('MailTrainer', null, [
                'label' => 'E-mail coach',
                'help' => 'Deze e-mail wordt gebruikt om de coach te informeren over de inschrijvingen van het team.',
                'attr' => [
                    'placeholder' => 'E-mail adres van de coach',
                    'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$',
                ]
            ])
            ->add('TrainerTel', null, [
                'label' => 'Telefoon nr. coach',
                'attr' => [
                    'placeholder' => 'Telefoonnummer van de coach'
                ]
            ])
            ->add('TrainerBirthDay', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Geboortedatum coach',
                'attr' => [
                    'placeholder' => 'Geboortedatum van de coach'
                ],
            ])
            ->add('Category', EntityType::class, [
                'class' => 'App\Entity\DanceCategory',
                'label' => 'Categorie',
                'choice_label' => 'Name',
                'placeholder' => 'Selecteer categorie',
                'required' => true,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {

        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
