<?php

namespace AerialShip\SteelMqBundle\Model\Repository;

use AerialShip\SteelMqBundle\Entity\Message;
use AerialShip\SteelMqBundle\Entity\Queue;

interface MessageRepositoryInterface
{
    /**
     * @param Message $message
     * @param bool $flush
     * @return void
     */
    public function save(Message $message, $flush = true);

    /**
     * @param Queue $queue
     * @return int
     */
    public function clearQueue(Queue $queue);
}
