<?php

namespace AerialShip\SteelMqBundle\Form\Type;

use AerialShip\SteelMqBundle\Form\Extension\DataTransformer\MapTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class HeaderType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addViewTransformer(new MapTransformer())
            ->add('key', 'text')
            ->add('values', 'collection', [
                    "type" => 'text',
                    'allow_add' => true
                ])
        ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'header';
    }
}
