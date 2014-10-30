<?php

namespace AerialShip\SteelMqBundle\Controller\Api;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\ProjectRole;
use AerialShip\SteelMqBundle\Entity\Queue;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @Route("/projects/{projectId}/queues{slash}")
 */
class QueueController extends AbstractApiController
{
    /**
     * @Route("")
     * @Method({"POST"})
     * @ParamConverter("project", options={"id" = "projectId"})
     */
    public function createAction(Project $project, Request $request)
    {
        if (false == $this->get('security.context')->isGranted(ProjectRole::PROJECT_ROLE_QUEUE, $project)) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm('queue');

        $form->handleRequest($request);

        if (false == $form->isValid()) {
            return $this->handleView($this->view($form, 400));
        }

        /** @var Queue $queue */
        $queue = $form->getData();
        $this->get('aerial_ship_steel_mq.defaulter.queue')->setDefaults($queue);
        $queue->setProject($project);

        $em = $this->getEntityManager();

        $em->persist($queue);
        $em->flush();

        return $this->handleView($this->view($queue));
    }

}
