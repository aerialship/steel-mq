<?php

namespace AerialShip\SteelMqBundle\Services\Manager;

use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Entity\Subscriber;
use AerialShip\SteelMqBundle\Model\Repository\SubscriberRepositoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class SubscriberManager
{
    /** @var SecurityContextInterface */
    protected $securityContext;

    /** @var SubscriberRepositoryInterface */
    protected $subscriberRepository;

    /**
     * @param SubscriberRepositoryInterface $subscriberRepository
     * @param SecurityContextInterface      $securityContext
     */
    public function __construct(
        SubscriberRepositoryInterface $subscriberRepository,
        SecurityContextInterface $securityContext
    ) {
        $this->subscriberRepository = $subscriberRepository;
        $this->securityContext = $securityContext;
    }

    /**
     * @param Queue      $queue
     * @param Subscriber $subscriber
     */
    public function create(Queue $queue, Subscriber $subscriber)
    {
        $subscriber->setQueue($queue);
        $this->subscriberRepository->save($subscriber);
    }

    /**
     * @param Subscriber $subscriber
     */
    public function delete(Subscriber $subscriber)
    {
        $this->subscriberRepository->delete($subscriber);
    }

    public function getList(Queue $queue, $limit = 100, $offset = 0)
    {
        $result = array();
        foreach ($queue->getSubscribers()->slice($offset, $limit) as $subscriber) {
            $result[] = $subscriber;
        }

        return $result;
    }
}
