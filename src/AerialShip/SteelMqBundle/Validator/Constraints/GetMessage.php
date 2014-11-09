<?php

namespace AerialShip\SteelMqBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\Type;

class GetMessage extends Collection
{
    public function __construct()
    {
        parent::__construct(array(
            'fields' => array(
                'limit' => array(
                    new Type(array('type' => 'integer')),
                    new GreaterThanOrEqual(array('value' => 1)),
                    new LessThanOrEqual(array('value' => 100)),
                ),
                'timeout' => array(
                    new Type(array('type' => 'integer')),
                    new GreaterThanOrEqual(array('value' => 5)),
                    new LessThanOrEqual(array('value' => 3600)),
                ),
                'delete' => array(
                    new Type(array('type' => 'bool')),
                ),
            ),
        ));
    }

    public function validatedBy()
    {
        return 'Symfony\Component\Validator\Constraints\CollectionValidator';
    }
}
