<?php

namespace AerialShip\SteelMqBundle\Helper;

use Symfony\Component\HttpFoundation\ParameterBag;

final class RequestHelper
{
    /**
     * @param ParameterBag $bag
     * @param array        $values
     */
    public static function ensure(ParameterBag $bag, array $values)
    {
        foreach ($values as $k => $v) {
            if (is_int($k)) {
                $key = $v;
                $value = null;
            } else {
                $key = $k;
                $value = $v;
            }
            if (false === is_string($key)) {
                continue;
            }
            if (false === $bag->has($key)) {
                $bag->set($key, $value);
            }
        }
    }
}
