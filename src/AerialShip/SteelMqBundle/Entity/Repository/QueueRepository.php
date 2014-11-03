<?php

namespace AerialShip\SteelMqBundle\Entity\Repository;

use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Model\Repository\QueueRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class QueueRepository extends EntityRepository implements QueueRepositoryInterface
{
    /**
     * @param $callback
     * @return mixed
     */
    public function transactional($callback)
    {
        return $this->_em->transactional($callback);
    }

    /**
     * @param Queue $queue
     * @param bool $flush
     * @return void
     */
    public function save(Queue $queue, $flush = true)
    {
        $this->_em->persist($queue);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function delete(Queue $queue)
    {
        $this->_em->remove($queue);
        $this->_em->flush();
    }

}
