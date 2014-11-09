<?php

namespace AerialShip\SteelMqBundle\Services\Defaulter;

use AerialShip\SteelMqBundle\Entity\Message;

class MessageDefaulter
{
    /**
     * @param  Message $message
     * @return Message
     */
    public function setDefaults(Message $message)
    {
        if (null === $message->getQueue()) {
            throw new \LogicException('Message must have queue set in order to be able to set its defaults');
        }

        if (null === $message->getAvailableAt()) {
            $message->setDelay($message->getQueue()->getDelay());
        }
        if (null == $message->getRetriesRemaining()) {
            $message->setRetries($message->getQueue()->getRetries());
        }

        return $message;
    }
}
