<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DansschoolPopupType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('DansSchoolNaam', TextType::class, array(
            'mapped' => false,
            'required' => false,
            'label' => 'Dansschool',
            'attr' => array(
                'class' => 'form-control',
                'placeholder' => 'Naam van jouw dansschool',
            ),
        ));
    }
}