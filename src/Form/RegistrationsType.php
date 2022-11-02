<?php

namespace App\Form;

use App\Entity\Registrations;
use App\Entity\Team;
use App\Repository\ContestRepository;
use App\Repository\OrganisationRepository;
use App\Repository\RegistrationsRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class RegistrationsType extends AbstractType
{
    private $token;
    private $em;
    private $security;

    public function __construct(TokenStorageInterface $token, EntityManagerInterface $em, Security $security)
    {
        $this->token = $token;
        $this->em = $em;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!in_array('ROLE_ADMIN', $this->token->getToken()->getUser()->getRoles())) {
            $this->getFormBuilder($builder, $options);
            $builder->add('Dancers', DancersAutocompleteField::class, [
                'label' => 'Dansers',
                'required' => true,
                'placeholder' => 'Selecteer dansers',
            ]);
        } else {
            $this->getFormBuilder($builder, $options);
            $builder->add('Dancers', DancersAutocompleteField::class, [
                'label' => 'Dansers',
                'required' => true,
                'placeholder' => 'Selecteer dansers',
            ]);
        }

        if (!$options["register"]) {
            $builder->add('Contest', null, [
                'label' => 'Wedstrijd',
                'required' => true,
            ]);
        }

        $builder
            ->add('Music', DropzoneType::class, [
                'label' => 'Muziek bestand',
                'attr' => [
                    'placeholder' => 'Selecteer of drop hier je muziekbestand (.mp3) maximaal 512mb',
                ],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '512M',
                        'mimeTypes' => [
                            'audio/mpeg',
                            'audio/mp3',
                            'audio/wav',
                            'audio/x-wav',
                        ],
                        'mimeTypesMessage' => 'Upload een geldig .mp3 bestand!',
                    ])
                ],
            ])
            ->add('Comments', TextareaType::class, [
                'label' => 'Opmerkingen',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Registrations::class,
            'register' => false,
            'current_id' => null,
            'contest' => null,
            'edit' => false,
            'edit-page' => false,
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function getFormBuilder(FormBuilderInterface $builder, array $options): void
    {
        if($options["edit-page"]) {
            return;
        }
        $builder->add('Team', EntityType::class, [
            'class' => Team::class,
            'choice_label' => 'Name',
            'placeholder' => 'Selecteer een team',
            'required' => true,
            'query_builder' => function (TeamRepository $teamRepository) use ($options) {
                $qb = $teamRepository->createQueryBuilder('t');
                if($options["contest"]) {
                    $expr = $this->em->getExpressionBuilder();
                    $qb->andWhere(
                        $expr->notIn(
                            't.id',
                            $this->em->createQueryBuilder()
                                ->select('identity(tr.Team)')
                                ->from('App:Registrations', 'tr')
                                ->where('tr.Contest = :contest')
                                ->getDQL()
                        )
                    );
                    $qb->andWhere('t.User = :user');
                    $qb->setParameter('user', $this->security->getUser());
                    $qb->setParameter('contest', $options['contest']);
                }
                return $qb;
            },
            'autocomplete' => true,
        ]);
    }
}
