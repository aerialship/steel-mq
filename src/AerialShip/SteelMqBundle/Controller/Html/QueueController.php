<?php

namespace AerialShip\SteelMqBundle\Controller\Html;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Helper\SecurityHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class QueueController extends Controller
{
    /**
     * @Route("/view/{projectId}/queue/{queueId}/messages", name="messages")
     * @ParamConverter("project", options={"id" = "projectId"})
     * @ParamConverter("queue", options={"id" = "queueId"})
     * @SecureParam(name="project", permissions="PROJECT_ROLE_DEFAULT")
     */
    public function listAction(Project $project, Queue $queue)
    {
        SecurityHelper::checkQueueIsInProject($project, $queue);

        return $this->render('@AerialShipSteelMq/queue/messages.html.twig', array(
            'queue' => $queue,
        ));
    }
}
