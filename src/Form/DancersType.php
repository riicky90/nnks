<?php

namespace App\Form;

use App\Entity\Dancers;
use App\Entity\Team;
use App\Repository\TeamRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class DancersType extends AbstractType
{
    private $user;

    public function __construct(Security $user)
    {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Team', EntityType::class, [
                'class' => Team::class,
                'query_builder' => function (TeamRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->where('t.User = :user')
                        ->setParameter('user', $this->user->getUser());
                },
                'choice_label' => 'Name',
                'choice_value' => 'Id',
                'label' => 'Team',
                'required' => true,
            ])
            ->add('FirstName', TextType::class, [
                'label' => 'Voornaam',
                'attr' => [
                    'placeholder' => 'Voornaam van de danser'
                ],
                'required' => true
            ])
            ->add('SecondName', TextType::class, [
                'label' => 'Tussenvoegsel',
                'attr' => [
                    'placeholder' => 'Tussenvoegsel van de danser'
                ],
                'required' => false
            ])
            ->add('LastName', TextType::class, [
                'label' => 'Achternaam',
                'attr' => [
                    'placeholder' => 'Achternaam van de danser'
                ],
                'required' => true
            ])
            ->add('BirthDay', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Geboortedatum',
                'attr' => [
                    'placeholder' => 'Geboortedatum van de danser'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dancers::class,
        ]);
    }
}
