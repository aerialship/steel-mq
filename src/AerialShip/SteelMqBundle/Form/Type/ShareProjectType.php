<?php

namespace AerialShip\SteelMqBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ShareProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roles', 'bootstrap_collection', array(
                'type' => 'project_collaborator',
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'add_button_text' => 'Add more users',
                'delete_button_text' => '×',
                'options' => array(
                    'attr' => array('style' => 'inline'),
                ),
            ))
        ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'share_project';
    }
}
