<?php

namespace AerialShip\SteelMqBundle\Helper;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Helper
{
    /**
     * @return string
     */
    public static function generateToken()
    {
        return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getPostJson(Request $request)
    {
        $json = json_decode($request->getContent(), true);

        if (false == is_array($json)) {
            throw new BadRequestHttpException('Body parameters not supplied');
        }

        return $json;
    }
}
