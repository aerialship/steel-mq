<?php

namespace AerialShip\SteelMqBundle\Model\Repository;

use AerialShip\SteelMqBundle\Entity\Message;

interface MessageRepositoryInterface
{
    /**
     * @param  Message $message
     * @param  bool    $flush
     * @return void
     */
    public function save(Message $message, $flush = true);

}
