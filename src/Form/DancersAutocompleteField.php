<?php

namespace App\Form;

use App\Entity\Dancers;
use App\Repository\DancersRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class DancersAutocompleteField extends AbstractType
{
    private $user;

    //constructor with security dependency
    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        if(in_array('ROLE_ADMIN', $this->user->getRoles())){
            $resolver->setDefaults([
                'class' => Dancers::class,
                'placeholder' => 'Kies dansers',
                'multiple' => true,
                'choice_label' => 'AllDetails',
                'query_builder' => function (DancersRepository $dancersRepository) {
                    return $dancersRepository->createQueryBuilder('d')
                        ->orderBy('d.FirstName', 'ASC')
                        ->leftJoin('d.team', 't');
                },
            ]);
        }else {
            $resolver->setDefaults([
                'class' => Dancers::class,
                'placeholder' => 'Kies dansers',
                'multiple' => true,
                'choice_label' => 'AllDetails',
                'query_builder' => function (DancersRepository $dancersRepository) {
                    return $dancersRepository->createQueryBuilder('d')
                        ->orderBy('d.FirstName', 'ASC')
                        ->leftJoin('d.team', 't')
                        ->andWhere('t.User = :user')
                        ->setParameter('user', $this->user);
                },
            ]);
        }
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
