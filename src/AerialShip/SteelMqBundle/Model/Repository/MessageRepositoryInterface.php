<?php

namespace AerialShip\SteelMqBundle\Model\Repository;

use AerialShip\SteelMqBundle\Entity\Message;
use AerialShip\SteelMqBundle\Entity\Queue;

interface MessageRepositoryInterface
{
    /**
     * @param $callback
     * @return mixed
     */
    public function transactional($callback);

    /**
     * @param  Message $message
     * @param  bool    $flush
     * @return void
     */
    public function save(Message $message, $flush = true);
    /**
     * @param  array        $criteria
     * @param  array        $orderBy
     * @param  int|null     $limit
     * @param  int|null     $offset
     * @return Message|null
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param  array        $criteria
     * @param  array        $orderBy
     * @return Message|null
     */
    public function findOneBy(array $criteria, array $orderBy = null);

    /**
     * @param  int          $id
     * @return Message|null
     */
    public function find($id);

    /**
     * @param  Queue     $queue
     * @param  array     $options
     * @return Message[]
     */
    public function getMessages(Queue $queue, array $options);
}
