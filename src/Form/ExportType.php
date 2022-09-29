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
                'query_builder' => function (ContestRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->leftJoin('c.registrations', 'r')
                        ->having('count(r.id) <= 0');
                },
                'autocomplete' => true,
                'multiple' => false,
                'choice_label' => 'Name',
                'choice_value' => 'Id',
                'label' => 'Wedstrijd',
            ]);
    }
}