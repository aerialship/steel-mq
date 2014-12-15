<?php

namespace AerialShip\SteelMqBundle\Helper;

use AerialShip\SteelMqBundle\Entity\Message;
use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Entity\Subscriber;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class SecurityHelper
{
    /**
     * @param Project $project
     * @param Queue   $queue
     *
     * @throws BadRequestHttpException
     */
    public static function checkQueueIsInProject(Project $project, Queue $queue)
    {
        if ($queue->getProject()->getId() != $project->getId()) {
            throw new BadRequestHttpException();
        }
    }

    /**
     * @param Queue   $queue
     * @param Message $message
     *
     * @throws BadRequestHttpException
     */
    public static function checkMessageIsInQueue(Queue $queue, Message $message)
    {
        if ($message->getQueue()->getId() != $queue->getId()) {
            throw new BadRequestHttpException();
        }
    }

    /**
     * @param Queue      $queue
     * @param Subscriber $subscriber
     *
     * @throws BadRequestHttpException
     */
    public static function checkSubscriberIsInQueue(Queue $queue, Subscriber $subscriber)
    {
        if ($subscriber->getQueue()->getId() !== $queue->getId()) {
            throw new BadRequestHttpException();
        }
    }
}
