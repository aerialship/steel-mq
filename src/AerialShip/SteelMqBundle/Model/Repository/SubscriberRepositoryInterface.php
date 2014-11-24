<?php

namespace AerialShip\SteelMqBundle\Model\Repository;

use AerialShip\SteelMqBundle\Entity\Subscriber;

interface SubscriberRepositoryInterface
{
    /**
     * @param $callback
     * @return mixed
     */
    public function transactional($callback);

    /**
     * @param Subscriber $subscriber
     * @param bool       $flush
     */
    public function save(Subscriber $subscriber, $flush = true);

    /**
     * @param Subscriber $subscriber
     */
    public function delete(Subscriber $subscriber);
}
