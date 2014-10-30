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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        if (false == $this->get('security.context')->isGranted(ProjectRole::PROJECT_ROLE_DEFAULT, $project)) {
            throw new NotFoundHttpException();
        }

        $result = array();
        foreach ($project->getQueues() as $queue) {
            $result[] = $queue;
        }

        return $this->handleView(
            $this->view($result)
        );
    }

    /**
     * @Route("{slash}")
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

    /**
     * @Route("/{queueId}{slash}")
     * @Method({"POST"})
     * @ParamConverter("project", options={"id" = "projectId"})
     * @ParamConverter("queue", options={"id" = "queueId"})
     */
    public function updateAction(Project $project, Queue $queue, Request $request)
    {
        if (false == $this->get('security.context')->isGranted(ProjectRole::PROJECT_ROLE_QUEUE, $project)) {
            throw new AccessDeniedHttpException();
        }
        if ($queue->getProject()->getId() != $project->getId()) {
            throw new BadRequestHttpException();
        }

        $form = $this->createForm('queue', $queue);

        $form->submit($request, false);

        if (false == $form->isValid()) {
            return $this->handleView($this->view($form, 400));
        }

        $this->get('aerial_ship_steel_mq.defaulter.queue')->setDefaults($queue);

        $em = $this->getEntityManager();

        $em->persist($queue);
        $em->flush();

        return $this->handleView($this->view($queue));
    }
}
