<?php

namespace AerialShip\SteelMqBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConfirmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['confirm']) {
            $builder->add('confirm', 'submit', array(
                'label' => $options['confirm_label'],
                'button_class' => $options['confirm_button_class'],
                'attr' => array(
                    'icon' => $options['confirm_icon'],
                ),
            ));
        }
        if ($options['cancel']) {
            $builder->add('cancel', 'submit', array(
                'label' => $options['cancel_label'],
                'button_class' => $options['cancel_button_class'],
                'attr' => array(
                    'icon' => $options['cancel_icon'],
                ),
            ));
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'confirm' => true,
            'confirm_label' => ' Confirm',
            'confirm_icon' => 'check',
            'confirm_button_class' => 'primary',
            'cancel_label' => ' Cancel',
            'cancel_icon' => 'cancel',
            'cancel_button_class' => 'default',
            'cancel' => true,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'confirm';
    }
}
