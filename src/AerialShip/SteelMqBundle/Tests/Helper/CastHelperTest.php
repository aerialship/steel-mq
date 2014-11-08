<?php

namespace AerialShip\SteelMqBundle\Tests\Helper;

use AerialShip\SteelMqBundle\Helper\CastHelper;

class CastHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testAsUserReturnsUser()
    {
        $user = CastHelper::asUser($this->getUserMock());
        $this->assertNotNull($user);
        $this->assertInstanceOf('AerialShip\SteelMqBundle\Entity\User', $user);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected User
     */
    public function testAsUserThrowsIfNull()
    {
        CastHelper::asUser(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected User
     */
    public function testAsUserThrowsIfObject()
    {
        CastHelper::asUser(new \stdClass());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected User
     */
    public function testAsUserThrowsIfUserInterface()
    {
        CastHelper::asUser($this->getUserInterfaceMock());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\AerialShip\SteelMqBundle\Entity\User
     */
    private function getUserMock()
    {
        return $this->getMock('AerialShip\SteelMqBundle\Entity\User');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Core\User\UserInterface
     */
    private function getUserInterfaceMock()
    {
        return $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
    }

}
