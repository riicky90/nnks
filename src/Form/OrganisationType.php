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
            ])
            ->add('Adres')
            ->add('ZipCode', null, [
                'label' => 'Postcode',
            ])
            ->add('City', null, [
                'label' => 'Plaats',
            ])
            ->add('Email', null, [
                'label' => 'E-mail',
            ])
            ->add('Phone', null, [
                'label' => 'Telefoon',
            ])
            ->add('MollieApiKey', null, [
                'label' => 'Mollie API Sleutel',
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
