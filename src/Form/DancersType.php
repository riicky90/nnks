<?php

namespace App\Form;

use App\Entity\Dancers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DancersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('FirstName', TextType::class, [
                'label' => 'Voornaam',
                'required' => true
            ])
            ->add('SecondName', TextType::class, [
                'label' => '2e naam',
                'required' => false
            ])
            ->add('LastName', TextType::class, [
                'label' => 'Achternaam',
                'required' => true
            ])
            ->add('BirthDay', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Geboortedatum',
                'attr' => [
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'd-m-y'
                ],
                'input_format' => 'd-m-Y'
            ])
            ->add('registrations');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dancers::class,
        ]);
    }
}
