<?php

namespace App\Form;

use App\Entity\Dancers;
use App\Repository\DancersRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class DancersAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Dancers::class,
            'placeholder' => 'Kies dansers',
            'multiple' => true,
            'choice_label' => 'AllDetails'
        ]);
            //'security' => 'ROLE_SOMETHING',);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
