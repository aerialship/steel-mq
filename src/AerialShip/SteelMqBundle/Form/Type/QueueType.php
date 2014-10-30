<?php

namespace AerialShip\SteelMqBundle\Form\Type;

use AerialShip\SteelMqBundle\Entity\Queue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QueueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text')
            ->add('pushType', 'choice', array(
                'required' => false,
                'choices' => array(Queue::PUSH_TYPE_PULL, Queue::PUSH_TYPE_MULTICAST, Queue::PUSH_TYPE_UNICAST),
            ))
            ->add('retries', 'integer', array(
                'required' => false,
            ))
            ->add('retriesDelay', 'integer', array(
                'required' => false,
            ))
            ->add('errorQueue', 'integer', array(
                'required' => false,
            ))
            ->add('timeout', 'integer', array(
                'required' => false,
            ))
            ->add('delay', 'integer', array(
                'required' => false,
            ))
            ->add('expiresIn', 'integer', array(
                'required' => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AerialShip\SteelMqBundle\Entity\Queue',
            'csrf_protection'   => false,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'queue';
    }

}