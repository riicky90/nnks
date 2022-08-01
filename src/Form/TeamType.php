<?php

namespace App\Form;

use App\Entity\DanceCategory;
use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        if($options['show_organisation']) {
            $builder
                ->add('Organisation', EntityType::class, [
                    'class' => 'App\Entity\Organisation',
                    'choice_label' => 'Name',
                    'placeholder' => 'Select an organisation',
                    'required' => true
                ]);
        }
        $builder
            ->add('Name')
            ->add('TrainerName')
            ->add('MailTrainer')
            ->add('Comments')
            ->add('Category');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {

        $resolver->setDefaults([
            'data_class' => Team::class,
            'show_organisation' => true
        ]);
    }
}
