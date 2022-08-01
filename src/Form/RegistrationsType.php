<?php

namespace App\Form;

use App\Entity\Registrations;
use App\Entity\Team;
use App\Repository\ContestRepository;
use App\Repository\OrganisationRepository;
use App\Repository\RegistrationsRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\File;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class RegistrationsType extends AbstractType
{
    private $token;
    private $organisation;
    private $contest;
    private $reg;

    public function __construct(TokenStorageInterface $token, RegistrationsRepository $registrations, OrganisationRepository $organisationRepo, ContestRepository $contestRepository)
    {
        $this->token = $token;

        $this->contest = $contestRepository;
        $this->organisation = $organisationRepo->find($this->token->getToken()->getUser()->getOrganisation());
        $this->reg = $registrations;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!in_array('ROLE_ADMIN', $this->token->getToken()->getUser()->getRoles())) {
            $builder->add('Team', EntityType::class, [
                'class' => Team::class,
                'attr' => [
                    "data-action" => "live#update"
                ],
                'query_builder' => function (EntityRepository $er) {

                    $teams = $er->createQueryBuilder('t')
                        ->leftJoin('t.registrations', 'r')
                        ->andWhere('t.Organisation = :organisation')
                        ->setParameter('organisation', $this->organisation);

                    return $teams;
                }
            ]);
            $builder->add('Dancers');
        } else {
            $builder->add('Team');
            $builder->add('Dancers');
        }

        if (!$options["register"]) {
            $builder->add('Contest');
        }

        $builder
            ->add('Music', DropzoneType::class, [
                'label' => 'Muziek bestand',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1G',
                        'mimeTypes' => [
                            'audio/mpeg',
                            'audio/mp3',
                            'audio/wav',
                            'audio/x-wav',
                        ],
                        'mimeTypesMessage' => 'Upload een geldig .mp3 of .wav bestand!',
                    ])
                ],
            ])
            ->add('Comments');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Registrations::class,
            'register' => false,
            'current_id' => null,
        ]);
    }
}
