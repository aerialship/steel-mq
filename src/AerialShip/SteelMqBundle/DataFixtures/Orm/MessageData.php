<?php

namespace AerialShip\SteelMqBundle\DataFixtures\Orm;

use AerialShip\SteelMqBundle\Entity\Message;
use AerialShip\SteelMqBundle\Entity\Queue;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class MessageData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /** @var Queue $queue */
        $queue = $this->getReference('queue-p1-1');

        $message = new Message();
        $message
            ->setQueue($queue)
            ->setBody('message 1')
            ->setCreatedAt(new \DateTime('-60 minutes'))
            ->setAvailableAt(new \DateTime('-60 minutes'))
            ->setRetriesRemaining($queue->getRetries())
        ;
        $manager->persist($message);

        $message = new Message();
        $message
            ->setQueue($queue)
            ->setBody('message 2')
            ->setCreatedAt(new \DateTime('-50 minutes'))
            ->setAvailableAt(new \DateTime('-50 minutes'))
            ->setRetriesRemaining($queue->getRetries())
        ;
        $manager->persist($message);

        $message = new Message();
        $message
            ->setQueue($queue)
            ->setBody('message 3')
            ->setCreatedAt(new \DateTime('-50 minutes'))
            ->setAvailableAt(new \DateTime('-40 minutes'))
            ->setRetriesRemaining($queue->getRetries())
        ;
        $manager->persist($message);

        $manager->flush();
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
