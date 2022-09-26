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
        $builder
            ->add('Name')
            ->add('TrainerName')
            ->add('Category', EntityType::class, [
                'class' => 'App\Entity\DanceCategory',
                'choice_label' => 'Name',
                'placeholder' => 'Selecteer categorie',
                'required' => true
            ])
            ->add('MailTrainer')
            ->add('Comments');

    }

    public function configureOptions(OptionsResolver $resolver): void
    {

        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
