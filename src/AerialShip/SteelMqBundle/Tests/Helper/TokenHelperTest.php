<?php

namespace AerialShip\SteelMqBundle\Tests\Helper;

use AerialShip\SteelMqBundle\Helper\TokenHelper;

class TokenHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateLength()
    {
        $token = TokenHelper::generate();
        $this->assertEquals(31, strlen($token));
    }

    public function testGenerateVariety()
    {
        $token = TokenHelper::generate();
        $len = strlen($token);
        $arr = array();
        for ($i = 0; $i<$len; $i++) {
            $arr[$token[$i]] = 1;
        }
        $this->assertGreaterThan(10, count($arr));
    }
}
