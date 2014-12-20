<?php

namespace AerialShip\SteelMqBundle\Services\Manager;

use AerialShip\SteelMqBundle\Entity\Message;
use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Model\Repository\MessageRepositoryInterface;
use AerialShip\SteelMqBundle\Model\Repository\QueueRepositoryInterface;
use AerialShip\SteelMqBundle\Services\Defaulter\GetMessageDefaulter;
use AerialShip\SteelMqBundle\Services\Defaulter\MessageDefaulter;
use AerialShip\SteelMqBundle\Services\Defaulter\ReleaseMessageDefaulter;
use AerialShip\SteelMqBundle\Validator\Constraints\GetMessage;
use AerialShip\SteelMqBundle\Validator\Constraints\ReleaseMessage;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MessageManager
{
    /** @var ValidatorInterface */
    protected $validator;

    /** @var MessageRepositoryInterface */
    protected $messageRepository;

    /** @var QueueRepositoryInterface */
    protected $queueRepository;

    /** @var MessageDefaulter */
    protected $messageDefaulter;

    /** @var GetMessageDefaulter */
    protected $getMessageDefaulter;

    /** @var ReleaseMessageDefaulter */
    protected $releaseMessageDefaulter;

    /**
     * @param ValidatorInterface         $validator
     * @param MessageRepositoryInterface $messageRepository
     * @param QueueRepositoryInterface   $queueRepository
     * @param MessageDefaulter           $messageDefaulter
     * @param GetMessageDefaulter        $getMessageDefaulter
     * @param ReleaseMessageDefaulter    $releaseMessageDefaulter
     */
    public function __construct(
        ValidatorInterface $validator,
        MessageRepositoryInterface $messageRepository,
        QueueRepositoryInterface $queueRepository,
        MessageDefaulter $messageDefaulter,
        GetMessageDefaulter $getMessageDefaulter,
        ReleaseMessageDefaulter $releaseMessageDefaulter
    ) {
        $this->validator = $validator;
        $this->messageRepository = $messageRepository;
        $this->queueRepository = $queueRepository;
        $this->messageDefaulter = $messageDefaulter;
        $this->getMessageDefaulter = $getMessageDefaulter;
        $this->releaseMessageDefaulter = $releaseMessageDefaulter;
    }

    /**
     * @param Queue $queue
     * @param array $data
     *
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
     * @param Queue $queue
     * @param array $options
     *
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

    /**
     * @param Message $message
     */
    public function delete(Message $message)
    {
        $this->messageRepository->transactional(function () use ($message) {
            $message->getQueue()->setDeletedCount($message->getQueue()->getDeletedCount() + 1);
            $this->queueRepository->save($message->getQueue());
            $this->messageRepository->delete($message);
        });
    }

    /**
     * @param Message $message
     * @param array   $options
     *
     * @return ConstraintViolationListInterface[]|true
     */
    public function release(Message $message, array $options)
    {
        $this->releaseMessageDefaulter->setDefaults($message->getQueue(), $options);
        $errors = $this->validator->validate($options, new ReleaseMessage());
        if ($errors->count()) {
            return $errors;
        }

        $this->messageRepository->release($message, $options['delay']);

        return true;
    }
}
