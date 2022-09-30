<?php

namespace App\Form;

use App\Repository\ContestRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Entity\Contest;

class ExportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Contest', EntityType::class, [
                'class' => Contest::class,
                'mapped' => false,
                'required' => true,
                'autocomplete' => true,
                'multiple' => false,
                'choice_value' => 'Id',
                'label' => 'Wedstrijd',
            ]);
    }
}