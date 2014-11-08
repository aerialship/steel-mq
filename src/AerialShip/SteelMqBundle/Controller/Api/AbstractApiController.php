<?php

namespace AerialShip\SteelMqBundle\Controller\Api;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\Queue;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
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
    protected function checkPermission($permission, $object = null)
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
    protected function checkQueueIsInProject(Project $project, Queue $queue)
    {
        if ($queue->getProject()->getId() != $project->getId()) {
            throw new BadRequestHttpException();
        }
    }

    /**
     * @param array $extra
     * @return array
     */
    protected function getSuccessData($extra = array())
    {
        return array_merge(['success'=>true], $extra);
    }

    /**
     * @param mixed $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleData($data, array $groups = null)
    {
        $view = $this->view($data);

        if ($groups) {
            $view->setSerializationContext(
                SerializationContext::create()
                    ->setGroups($groups)
            );
        }

        return parent::handleView($view);
    }
}
