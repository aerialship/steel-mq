<?php

namespace AerialShip\SteelMqBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('body', 'textarea', array(
                'required' => true,
            ))
            ->add('retries', 'integer', array(
                'required' => false,
            ))
            ->add('delay', 'integer', array(
                'required' => false,
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AerialShip\SteelMqBundle\Entity\Message',
            'csrf_protection'   => false,
        ));
    }

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return 'message';
    }
}
