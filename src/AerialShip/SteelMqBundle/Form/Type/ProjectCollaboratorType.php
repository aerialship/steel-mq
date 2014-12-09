<?php

namespace AerialShip\SteelMqBundle\Form\Type;

use AerialShip\SteelMqBundle\Entity\ProjectRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProjectCollaboratorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'constraints' => array(
                    new NotBlank(),
                ),
            ))
            ->add('roles', 'choice', array(
                'expanded' => true,
                'multiple' => true,
                'choices' => array(
                    ProjectRole::PROJECT_ROLE_QUEUE => 'Manage Queues',
                    ProjectRole::PROJECT_ROLE_SUBSCRIBE => 'Manage Subscribers',
                    ProjectRole::PROJECT_ROLE_SHARE => 'Manage Users',
                ),
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'project_collaborator';
    }
}
