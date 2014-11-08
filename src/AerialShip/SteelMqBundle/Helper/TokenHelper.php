<?php

namespace AerialShip\SteelMqBundle\Helper;

class TokenHelper
{
    /**
     * @return string
     */
    public static function generate()
    {
        return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }

}
