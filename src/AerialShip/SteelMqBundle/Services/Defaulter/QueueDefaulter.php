<?php

namespace AerialShip\SteelMqBundle\Services\Defaulter;

use AerialShip\SteelMqBundle\Entity\Queue;

class QueueDefaulter
{
    public function setDefaults(Queue $queue)
    {
        if (false === $queue->getPushType()) {
            $queue->setPushType(Queue::PUSH_TYPE_PULL);
        }
        if (false === $queue->getRetries()) {
            $queue->setRetries(3);
        }
        if (false === $queue->getRetriesDelay()) {
            $queue->setRetriesDelay(600);
        }
        if (false === $queue->getTimeout()) {
            $queue->setTimeout(60);
        }
        if (false === $queue->getDelay()) {
            $queue->setDelay(0);
        }
        if (false === $queue->getExpiresIn()) {
            $queue->setExpiresIn(604800); // 7 days
        }
    }
}
