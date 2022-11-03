<?php

namespace App\Form;

use App\Entity\Contest;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name', null, [
                'label' => 'Naam wedstrijd',
                'attr' => [
                    'placeholder' => 'Voorronde, Finale, Sow What You Got etc.',
                ],
            ])
            ->add('Date', DateType::class, [
                'label' => 'Datum',
                'widget' => 'single_text',
                'attr' => [
                    'data-stripe' => '',
                    'placeholder' => 'Datum van de wedstrijd',
                ],
            ])
            ->add('Location', null, [
                'label' => 'Locatie',
                'attr' => [
                    'placeholder' => 'Naam van de locatie (bv:  Leek of Urk)',
                ],
            ])
            ->add('LocationAddress', null, [
                'label' => 'Locatie adres',
                'attr' => [
                    'placeholder' => 'Adres van de locatie',
                ],
            ])
            ->add('LocationZipCode', null, [
                'label' => 'Locatie postcode',
                'attr' => [
                    'placeholder' => 'Postcode van de locatie',
                    'class' => 'alginside',
                ],
            ])
            ->add('LocationCity', null, [
                'label' => 'Locatie plaats',
                'attr' => [
                    'placeholder' => 'Plaats van de locatie',
                    'class' => 'alginside',
                ],
            ])
            ->add('Organisation', null, [
                'label' => 'Organisatie',
                'attr' => [
                    'placeholder' => 'Naam van de organisatie',
                ],
            ])
            ->add('Description', TextareaType::class, [
                'label' => 'Beschrijving',
                'attr' => [
                    'placeholder' => 'Beschrijving voor de website',
                ],
            ])
            ->add('Disciplines', ChoiceType::class, [
                'label' => 'Disciplines',
                'choices' => Contest::DICIPLINESLIST,
                'placeholder' => 'Kies één of meerdere disciplines',
                'multiple' => true,
                'autocomplete' => true,
            ])
            ->add('RegistrationFee', null, [
                'label' => 'Inschrijfgeld'
            ])->add('RegistrationOpenFrom', DateType::class, [
                'label' => 'Inschrijving open vanaf',
                'widget' => 'single_text',
                'attr' => [
                    'data-stripe' => '',
                    'placeholder' => 'Datum start inschrijvingen',
                ],
            ])->add('Enabled', CheckboxType::class, [
                'label' => 'Wedstrijd publiceren'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contest::class,
        ]);
    }
}
