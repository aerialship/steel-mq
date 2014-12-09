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
    public function load(ObjectManager $manager)
    {

        $subscriber = new Subscriber();
        $headers = [
            [
                "name" => "Content-Type",
                "values" => [
                    "application/json",
                ],
            ],
            [
                "name" => "X-Custom",
                "values" => [
                    "foo",
                    "bar",
                ],
            ],
        ];
        $subscriber
            ->setUrl('http://some.subscriber.com/steal_mq_hook')
            ->setHeaders($headers)
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
    public function getOrder()
    {
        return 40;
    }
}
