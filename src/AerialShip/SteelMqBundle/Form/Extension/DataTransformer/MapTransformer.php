<?php

namespace AerialShip\SteelMqBundle\Form\Extension\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class MapTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (null == $value) {
            return [];
        }

        $key = array_keys($value)[0];
        $values = array_values($value)[0];

        return [
            "key" => $key,
            "values" => $values
        ];
    }

    public function reverseTransform($value)
    {
        return [$value['key'] => $value['values']];
    }

}
