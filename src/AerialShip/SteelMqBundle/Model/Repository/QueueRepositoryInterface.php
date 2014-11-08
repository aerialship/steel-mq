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
     * @param  Queue $queue
     * @param  bool  $flush
     * @return void
     */
    public function save(Queue $queue, $flush = true);

    /**
     * @param  Queue $queue
     * @return void
     */
    public function delete(Queue $queue);

    /**
     * @param  Queue $queue
     * @return int
     */
    public function clearQueue(Queue $queue);

    /**
     * @param  mixed      $id
     * @return Queue|null
     */
    public function find($id);

    /**
     * @param  array      $criteria
     * @param  array      $orderBy
     * @return Queue|null
     */
    public function findOneBy(array $criteria, array $orderBy = null);

    /**
     * @param  array    $criteria
     * @param  array    $orderBy
     * @param  int|null $limit
     * @param  int|null $offset
     * @return Queue[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

}
