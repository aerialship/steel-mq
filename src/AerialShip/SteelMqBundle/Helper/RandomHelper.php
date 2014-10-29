<?php

namespace AerialShip\SteelMqBundle\Helper;

class RandomHelper
{
    /**
     * @return string
     */
    public static function generateToken()
    {
        return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }
}
