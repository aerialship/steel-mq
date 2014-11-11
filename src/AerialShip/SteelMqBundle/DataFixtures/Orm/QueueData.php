<?php

namespace AerialShip\SteelMqBundle\DataFixtures\Orm;

use AerialShip\SteelMqBundle\Entity\Queue;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class QueueData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $queue = new Queue();
        $queue->setTitle('Main Queue P1')
            ->setProject($this->getReference('project-one'));
        if (null === $this->container) {
            throw new \LogicException('No container');
        }
        $this->container->get('aerial_ship_steel_mq.defaulter.queue')->setDefaults($queue);
        $manager->persist($queue);
        $manager->flush();
        $this->addReference('queue-p1-1', $queue);


        $queue = new Queue();
        $queue->setTitle('Queue Two P1')
            ->setProject($this->getReference('project-one'));
        if (null === $this->container) {
            throw new \LogicException('No container');
        }
        $this->container->get('aerial_ship_steel_mq.defaulter.queue')->setDefaults($queue);
        $manager->persist($queue);
        $manager->flush();
        $this->addReference('queue2-p1-1', $queue);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 30;
    }

}
