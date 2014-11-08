<?php

namespace AerialShip\SteelMqBundle\Entity\Repository;

use AerialShip\SteelMqBundle\Entity\Message;
use AerialShip\SteelMqBundle\Model\Repository\MessageRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class MessageRepository extends EntityRepository implements MessageRepositoryInterface
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
     * @param  Message $message
     * @param  bool    $flush
     * @return void
     */
    public function save(Message $message, $flush = true)
    {
        $this->_em->persist($message);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
