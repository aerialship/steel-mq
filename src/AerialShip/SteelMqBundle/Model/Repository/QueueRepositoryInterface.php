<?php

namespace AerialShip\SteelMqBundle\Model\Repository;

use AerialShip\SteelMqBundle\Entity\Queue;

interface QueueRepositoryInterface
{
    /**
     * @param $callback
     * @return mixed
     */
    public function transactional($callback);

    /**
     * @param Queue $queue
     * @param bool $flush
     * @return void
     */
    public function save(Queue $queue, $flush = true);

    /**
     * @param Queue $queue
     * @return void
     */
    public function delete(Queue $queue);
}
