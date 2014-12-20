<?php

namespace AerialShip\SteelMqBundle\Helper;

class TokenHelper
{
    /**
     * @return string
     */
    public static function generate()
    {
        return str_pad(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36), 31, '0', STR_PAD_LEFT);
    }
}
