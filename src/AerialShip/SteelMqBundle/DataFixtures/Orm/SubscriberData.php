<?php

namespace AerialShip\SteelMqBundle\DataFixtures\Orm;

use AerialShip\SteelMqBundle\Entity\Subscriber;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class SubscriberData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $subscriber = new Subscriber();
        $subscriber
            ->setUrl('http://some.subscriber.com/steal_mq_hook')
            ->addHeader("Content-Type", ["application/json"])
            ->addHeader("X-Custom", ["foo", "bar"])
            ->setQueue($this->getReference('queue-p1-1'))
        ;

        $manager->persist($subscriber);
        $manager->flush();
        $this->addReference('subscriber-queue-p1-1', $subscriber);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 40;
    }


} 