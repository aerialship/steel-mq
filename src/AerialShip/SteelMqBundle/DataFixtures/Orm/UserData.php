<?php

namespace AerialShip\SteelMqBundle\DataFixtures\Orm;

use AerialShip\SteelMqBundle\Entity\User;
use AerialShip\SteelMqBundle\Helper\TokenHelper;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class UserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('superadmin@aerialship.com')
            ->setName('Super Admin')
            ->setSalt(TokenHelper::generate())
            ->setPassword('abcdefgh123')
            ->setRoles(array(User::ROLE_SUPER_ADMIN))
            ->setAccessToken('superAdminToken')
        ;
        $manager->persist($user);
        $manager->flush();
        $this->addReference('super-admin-user', $user);

        $user = new User();
        $user->setEmail('admin@aerialship.com')
            ->setName('Admin')
            ->setSalt(TokenHelper::generate())
            ->setPassword('abcdefgh123')
            ->setRoles(array(User::ROLE_ADMIN))
            ->setAccessToken('adminToken')
        ;
        $manager->persist($user);
        $manager->flush();
        $this->addReference('admin-user', $user);

        $user = new User();
        $user->setEmail('user@aerialship.com')
            ->setName('User')
            ->setSalt(TokenHelper::generate())
            ->setPassword('abcdefgh123')
            ->setRoles(array(User::ROLE_USER))
            ->setAccessToken('userToken')
        ;
        $manager->persist($user);
        $manager->flush();
        $this->addReference('user-user', $user);

        $user = new User();
        $user->setEmail('guest@aerialship.com')
            ->setName('Guest')
            ->setSalt(TokenHelper::generate())
            ->setPassword('abcdefgh123')
            ->setRoles(array(User::ROLE_USER))
            ->setAccessToken('guestToken')
        ;
        $manager->persist($user);
        $manager->flush();
        $this->addReference('guest-user', $user);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 10;
    }
}
