<?php

namespace AerialShip\SteelMqBundle\Controller\Api;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Helper\RequestHelper;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/projects/{projectId}/queues/{queueId}/messages")
 */
class MessageController extends AbstractApiController
{
    /**
     * @Route("{slash}")
     * @Method({"POST"})
     * @ParamConverter("project", options={"id" = "projectId"})
     * @ParamConverter("queue", options={"id" = "queueId"})
     * @SecureParam(name="project", permissions="PROJECT_ROLE_DEFAULT")
     */
    public function addAction(Project $project, Queue $queue, Request $request)
    {
        $this->checkQueueIsInProject($project, $queue);
        RequestHelper::ensure($request->request, array('messages' => array()));
        RequestHelper::ensure($request->request, array('message_collection' => array('messages' => $request->request->get('messages'))));

        $form = $this->createForm('message_collection');
        $form->handleRequest($request);
        if (false === $form->isValid()) {
            return $this->handleView($this->view($form->createView(), 400));
        }

        $message = $this->get('aerial_ship_steel_mq.manager.message')->add($queue, $form->getData());

        return $this->handleData($message);
    }
    /**
     * @Route("{slash}")
     * @Method({"GET"})
     * @ParamConverter("project", options={"id" = "projectId"})
     * @ParamConverter("queue", options={"id" = "queueId"})
     * @SecureParam(name="project", permissions="PROJECT_ROLE_DEFAULT")
     */
    public function getAction(Project $project, Queue $queue, Request $request)
    {
        $this->checkQueueIsInProject($project, $queue);

        $options = array(
            'limit' => $request->query->get('limit'),
            'timeout' => $request->query->get('timeout'),
            'delete' => $request->query->get('delete'),
        );

        $result = $this->get('aerial_ship_steel_mq.manager.message')->getMessages($queue, $options);

        return $this->handleData($result);
    }
}
