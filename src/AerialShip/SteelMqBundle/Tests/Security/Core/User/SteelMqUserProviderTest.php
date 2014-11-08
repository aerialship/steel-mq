<?php

namespace AerialShip\SteelMqBundle\Tests\Security\Core\User;

use AerialShip\SteelMqBundle\Security\Core\User\SteelMqUserProvider;

class SteelMqUserProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementsUserProviderInterface()
    {
        $reflection = new \ReflectionClass('AerialShip\SteelMqBundle\Security\Core\User\SteelMqUserProvider');
        $this->assertTrue(
            $reflection->implementsInterface('Symfony\Component\Security\Core\User\UserProviderInterface')
        );
    }

    public function testConstructsWithRequiredArguments()
    {
        new SteelMqUserProvider($this->getUserRepositoryMock());
    }

    public function testLoadUserReturnsRepositoryResult()
    {
        $expectedUsername = 'username';
        $expectedUser = $this->getUserInterfaceMock();
        $userRepositoryMock = $this->getUserRepositoryMock();
        $userRepositoryMock->expects($this->once())
            ->method('getByUsername')
            ->with($expectedUsername)
            ->will($this->returnValue($expectedUser));

        $provider = new SteelMqUserProvider($userRepositoryMock);
        $user = $provider->loadUserByUsername($expectedUsername);

        $this->assertSame($expectedUser, $user);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadUserThrowsOnNullRepositoryResult()
    {
        $userRepositoryMock = $this->getUserRepositoryMock();
        $userRepositoryMock->expects($this->once())
            ->method('getByUsername')
            ->will($this->returnValue(null));

        $provider = new SteelMqUserProvider($userRepositoryMock);
        $provider->loadUserByUsername('username');
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function testRefreshThrowsOnUnsupportedUser()
    {
        $userRepositoryMock = $this->getUserRepositoryMock();
        $provider = new SteelMqUserProvider($userRepositoryMock);
        $provider->refreshUser($this->getUserInterfaceMock());
    }

    public function testRefreshReturnsRepositoryResult()
    {
        $userId = 123;

        $userRepositoryMock = $this->getUserRepositoryMock();
        $userRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($userId)
            ->will($this->returnValue($expectedUser = $this->getUserInterfaceMock()));

        $userMock = $this->getUserMock();
        $userMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($userId));

        $provider = new SteelMqUserProvider($userRepositoryMock);

        $user = $provider->refreshUser($userMock);

        $this->assertSame($expectedUser, $user);
    }

    public function testSupportsUserEntity()
    {
        $userRepositoryMock = $this->getUserRepositoryMock();
        $provider = new SteelMqUserProvider($userRepositoryMock);
        $this->assertTrue($provider->supportsClass('AerialShip\SteelMqBundle\Entity\User'));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\AerialShip\SteelMqBundle\Model\Repository\UserRepositoryInterface
     */
    private function getUserRepositoryMock()
    {
        return $this->getMock('AerialShip\SteelMqBundle\Model\Repository\UserRepositoryInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Core\User\UserInterface
     */
    private function getUserInterfaceMock()
    {
        return $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\AerialShip\SteelMqBundle\Entity\User
     */
    private function getUserMock()
    {
        return $this->getMock('AerialShip\SteelMqBundle\Entity\User');
    }
}
