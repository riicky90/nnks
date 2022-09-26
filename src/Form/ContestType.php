<?php

namespace App\Form;

use App\Entity\Contest;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name')
            ->add('Date', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'data-stripe' => ''
                ],
            ])
            ->add('Location')
            ->add('Organisation')
            ->add('Description')
            ->add('Enabled', null, [
                'label' => 'Actief'
            ])
            ->add('Disciplines')
            ->add('EntranceFee', null, [
                'label' => 'Entree geld'
            ])
            ->add('RegistrationFee', null, [
                'label' => 'Inschrijfgeld'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contest::class,
        ]);
    }
}
