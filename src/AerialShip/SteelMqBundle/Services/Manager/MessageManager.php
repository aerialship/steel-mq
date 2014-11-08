<?php

namespace AerialShip\SteelMqBundle\Services\Manager;

use AerialShip\SteelMqBundle\Entity\Message;
use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Model\Repository\MessageRepositoryInterface;
use AerialShip\SteelMqBundle\Services\Defaulter\MessageDefaulter;

class MessageManager
{
    /** @var MessageRepositoryInterface */
    protected $messageRepository;

    /** @var MessageDefaulter */
    protected $messageDefaulter;

    /**
     * @param MessageRepositoryInterface $messageRepository
     */
    public function __construct(MessageRepositoryInterface $messageRepository, MessageDefaulter $messageDefaulter)
    {
        $this->messageRepository = $messageRepository;
        $this->messageDefaulter = $messageDefaulter;
    }

    /**
     * @param  Queue     $queue
     * @param  array     $data
     * @return Message[]
     */
    public function add(Queue $queue, array $data)
    {
        $arr = array();
        foreach ($data['messages'] as $message) {
            if ($message instanceof Message) {
                $message->setQueue($queue);
                $this->messageDefaulter->setDefaults($message);
                $arr[] = $message;
            } else {
                throw new \LogicException('Expected Message');
            }
        }

        $this->messageRepository->transactional(function () use ($arr) {
            foreach ($arr as $message) {
                $this->messageRepository->save($message);
            }
        });

        return $arr;
    }
}
