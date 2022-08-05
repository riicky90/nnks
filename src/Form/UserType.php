<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Wachtwoord',
                'required' => false,
                'mapped' => false,
            ])
            ->add('Organisation')
            ->add('isVerified', ChoiceType::class, [
                'choices' => [
                    'Nee' => false,
                    'Ja' => true,
                ],
                'expanded' => false,
                'multiple' => false,
                'label' => 'E-mail bevestigd',
            ]);

        $builder->add('roles', ChoiceType::class, [
            'label' => 'Rol',
            'choices' => User::ROLES,
            'choice_translation_domain' => 'user',
            'multiple' => true,
            'expanded' => true,
            'required' => true,
        ]);
    }
}
