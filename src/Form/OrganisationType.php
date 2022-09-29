<?php

namespace App\Form;

use App\Entity\Organisation;
use App\Entity\Team;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganisationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name', null, [
                'label' => 'Naam',
                'attr' => [
                    'placeholder' => 'Naam van de organisatie'
                ]
            ])
            ->add('Adres', null, [
                'label' => 'Adres',
                'attr' => [
                    'placeholder' => 'Adres van de organisatie'
                ]
            ])
            ->add('ZipCode', null, [
                'label' => 'Postcode',
                'attr' => [
                    'placeholder' => 'Postcode van de organisatie',
                ]
            ])
            ->add('City', null, [
                'label' => 'Plaats',
                'attr' => [
                    'placeholder' => 'Plaats van de organisatie',
                ]
            ])
            ->add('Email', null, [
                'label' => 'E-mail',
                'attr' => [
                    'placeholder' => 'E-mail adres van de organisatie',
                ]
            ])
            ->add('Phone', null, [
                'label' => 'Telefoon',
                'attr' => [
                    'placeholder' => 'Telefoonnummer van de organisatie',
                ]
            ])
            ->add('Description', null, [
                'label' => 'Bechrijving dansschool',
                'attr' => [
                    'placeholder' => 'Beschrijving van de dansschool',
                ]
            ])
            ->add('MollieApiKey', null, [
                'label' => 'Mollie API Sleutel',
                'attr' => [
                    'placeholder' => 'Mollie API sleutel van de organisatie',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Organisation::class,
        ]);
    }
}
