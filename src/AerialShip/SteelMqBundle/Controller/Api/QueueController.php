<?php

namespace AerialShip\SteelMqBundle\Controller\Api;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\ProjectRole;
use AerialShip\SteelMqBundle\Entity\Queue;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/projects/{projectId}/queues")
 */
class QueueController extends AbstractApiController
{
    /**
     * @Route("{slash}")
     * @Method({"GET"})
     * @ParamConverter("project", options={"id" = "projectId"})
     */
    public function listAction(Project $project)
    {
        $this->checkPermission(ProjectRole::PROJECT_ROLE_DEFAULT, $project);

        $result = $this->get('aerial_ship_steel_mq.manager.queue')->getList($project);

        return $this->handleView(
            $this->view($result)
                ->setSerializationContext(
                    SerializationContext::create()
                        ->setGroups(array('Default'))
                )
        );
    }

    /**
     * @Route("{slash}")
     * @Method({"POST"})
     * @ParamConverter("project", options={"id" = "projectId"})
     */
    public function createAction(Project $project, Request $request)
    {
        $this->checkPermission(ProjectRole::PROJECT_ROLE_QUEUE, $project);

        $form = $this->createForm('queue');

        $form->handleRequest($request);

        if (false == $form->isValid()) {
            return $this->handleView($this->view($form, 400));
        }

        /** @var Queue $queue */
        $queue = $form->getData();

        $this->get('aerial_ship_steel_mq.manager.queue')->create($project, $queue);

        return $this->handleView(
            $this->view($queue)
                ->setSerializationContext(
                    SerializationContext::create()
                        ->setGroups(array('Default'))
                )
        );

    }

    /**
     * @Route("/{queueId}{slash}")
     * @Method({"POST"})
     * @ParamConverter("project", options={"id" = "projectId"})
     * @ParamConverter("queue", options={"id" = "queueId"})
     */
    public function updateAction(Project $project, Queue $queue, Request $request)
    {
        $this->checkPermission(ProjectRole::PROJECT_ROLE_QUEUE, $project);
        $this->checkQueueIsInProject($project, $queue);

        $form = $this->createForm('queue', $queue);

        $form->submit($request, false);

        if (false == $form->isValid()) {
            return $this->handleView($this->view($form, 400));
        }

        $this->get('aerial_ship_steel_mq.manager.queue')->update($project, $queue);

        return $this->handleView(
            $this->view($queue)
                ->setSerializationContext(
                    SerializationContext::create()
                        ->setGroups(array('Default'))
                )
        );
    }

    /**
     * @Route("/{queueId}{slash}")
     * @Method({"GET"})
     * @ParamConverter("project", options={"id" = "projectId"})
     * @ParamConverter("queue", options={"id" = "queueId"})
     */
    public function infoAction(Project $project, Queue $queue)
    {
        $this->checkPermission(ProjectRole::PROJECT_ROLE_QUEUE, $project);
        $this->checkQueueIsInProject($project, $queue);

        return $this->handleView(
            $this->view($queue)
                ->setSerializationContext(
                    SerializationContext::create()
                        ->setGroups(array('Default', 'size'))
                )
        );
    }

    /**
     * @Route("/{queueId}{slash}")
     * @Method({"DELETE"})
     * @ParamConverter("project", options={"id" = "projectId"})
     * @ParamConverter("queue", options={"id" = "queueId"})
     */
    public function deleteAction(Project $project, Queue $queue)
    {
        $this->checkPermission(ProjectRole::PROJECT_ROLE_QUEUE, $project);
        $this->checkQueueIsInProject($project, $queue);

        $this->get('aerial_ship_steel_mq.manager.queue')->delete($queue);

        return $this->handleView(
            $this->view(["success" => true])
        );
    }
}
