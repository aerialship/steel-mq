<?php

namespace AerialShip\SteelMqBundle\Controller\Api;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\Queue;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AbstractApiController extends FOSRestController
{
    /**
     * @param string     $permission
     * @param null|mixed $object
     *
     * @throws AccessDeniedHttpException
     */
    public function checkPermission($permission, $object = null)
    {
        if (false == $this->get('security.context')->isGranted($permission, $object)) {
            throw new AccessDeniedHttpException();
        }
    }

    /**
     * @param Project $project
     * @param Queue   $queue
     *
     * @throws BadRequestHttpException
     */
    public function checkQueueIsInProject(Project $project, Queue $queue)
    {
        if ($queue->getProject()->getId() != $project->getId()) {
            throw new BadRequestHttpException();
        }
    }
}
