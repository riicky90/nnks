<?php

namespace App\Form;

use App\Entity\Team;
use App\Repository\ContestRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class TeamAutocompleteField extends AbstractType
{
    private $security;
    private $em;
    private $requestStack;

    //constructor
    public function __construct(RequestStack $requestStack, Security $security, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->security = $security;
        $this->requestStack = $requestStack->getCurrentRequest()->get('contest');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Team::class,
            'placeholder' => 'Kies een team',
            'choice_label' => 'Name',

        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
