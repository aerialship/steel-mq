<?php

namespace AerialShip\SteelMqBundle\Controller\ApiRaw;

use AerialShip\SteelMqBundle\Controller\Api\AbstractApiController;
use AerialShip\SteelMqBundle\Helper\RequestHelper;
use AerialShip\SteelMqBundle\Helper\SecurityHelper;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\Queue;

/**
 * @Route("/projects/{projectId}/queues/{queueId}/messages")
 */
class MessageController extends AbstractApiController
{
    /**
     * @Route("/webhook{slash}", defaults={"_decoder": ""})
     * @Method({"POST"})
     * @ParamConverter("project", options={"id" = "projectId"})
     * @ParamConverter("queue", options={"id" = "queueId"})
     * @SecureParam(name="project", permissions="PROJECT_ROLE_DEFAULT")
     */
    public function webHookAction(Project $project, Queue $queue, Request $request)
    {
        SecurityHelper::checkQueueIsInProject($project, $queue);

        $body = $request->getContent();
        RequestHelper::ensure($request->request, array(
            'message_collection' => array(
                'messages' => array(
                    array(
                        'body' => $body,
                    ),
                ),
            ),
        ));

        $form = $this->createForm('message_collection');
        $form->handleRequest($request);
        if (false === $form->isValid()) {
            return $this->handleView($this->view($form->createView(), 400));
        }

        $message = $this->get('aerial_ship_steel_mq.manager.message')->add($queue, $form->getData());

        return $this->handleData($message);
    }
}
