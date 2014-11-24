<?php

namespace AerialShip\SteelMqBundle\Form\Type;

use AerialShip\SteelMqBundle\Entity\Queue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QueueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'required' => true,
            ))
            ->add('push_type', 'choice', array(
                'required' => false,
                'choices' => array(
                    Queue::PUSH_TYPE_PULL=>Queue::PUSH_TYPE_PULL,
                    Queue::PUSH_TYPE_MULTICAST=>Queue::PUSH_TYPE_MULTICAST,
                    Queue::PUSH_TYPE_UNICAST=>Queue::PUSH_TYPE_UNICAST,
                ),
            ))
            ->add('retries', 'integer', array(
                'required' => false,
            ))
            ->add('retries_delay', 'integer', array(
                'required' => false,
            ))
            ->add('error_queue', 'integer', array(
                'required' => false,
            ))
            ->add('timeout', 'integer', array(
                'required' => false,
            ))
            ->add('delay', 'integer', array(
                'required' => false,
            ))
            ->add('expires_in', 'integer', array(
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
            'data_class'        => 'AerialShip\SteelMqBundle\Entity\Queue',
            'csrf_protection'   => false,
        ));
    }

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return 'queue';
    }

}
