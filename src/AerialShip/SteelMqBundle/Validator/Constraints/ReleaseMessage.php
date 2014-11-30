<?php

namespace AerialShip\SteelMqBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\Type;

class ReleaseMessage extends Collection
{
    public function __construct()
    {
        parent::__construct(array(
            'fields' => array(
                'delay' => array(
                    new Type(array('type' => 'integer')),
                    new GreaterThanOrEqual(array('value' => 0)),
                    new LessThanOrEqual(array('value' => 86400)), // 1 day
                ),
            ),
        ));
    }

    public function validatedBy()
    {
        return 'Symfony\Component\Validator\Constraints\CollectionValidator';
    }
}
