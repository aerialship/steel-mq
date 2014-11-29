<?php

namespace AerialShip\SteelMqBundle\Services\Manager;

use AerialShip\SteelMqBundle\Entity\Message;
use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Model\Repository\MessageRepositoryInterface;
use AerialShip\SteelMqBundle\Services\Defaulter\GetMessageDefaulter;
use AerialShip\SteelMqBundle\Services\Defaulter\MessageDefaulter;
use AerialShip\SteelMqBundle\Validator\Constraints\GetMessage;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MessageManager
{
    /** @var ValidatorInterface */
    protected $validator;

    /** @var MessageRepositoryInterface */
    protected $messageRepository;

    /** @var MessageDefaulter */
    protected $messageDefaulter;

    /** @var GetMessageDefaulter */
    protected $getMessageDefaulter;

    /**
     * @param ValidatorInterface         $validator
     * @param MessageRepositoryInterface $messageRepository
     * @param MessageDefaulter           $messageDefaulter
     * @param GetMessageDefaulter        $getMessageDefaulter
     */
    public function __construct(ValidatorInterface $validator, MessageRepositoryInterface $messageRepository, MessageDefaulter $messageDefaulter, GetMessageDefaulter $getMessageDefaulter)
    {
        $this->validator = $validator;
        $this->messageRepository = $messageRepository;
        $this->messageDefaulter = $messageDefaulter;
        $this->getMessageDefaulter = $getMessageDefaulter;
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

    /**
     * @param  Queue                                      $queue
     * @param  array                                      $options
     * @return ConstraintViolationListInterface|Message[]
     */
    public function getMessages(Queue $queue, array $options)
    {
        $this->getMessageDefaulter->setDefaults($queue, $options);
        $errors = $this->validator->validate($options, new GetMessage());
        if ($errors->count()) {
            return $errors;
        }

        return $this->messageRepository->getMessages($queue, $options);
    }

    public function delete(Message $message)
    {
        $this->messageRepository->delete($message);
    }
}
