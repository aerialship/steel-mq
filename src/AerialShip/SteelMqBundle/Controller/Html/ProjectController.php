<?php

namespace AerialShip\SteelMqBundle\Controller\Html;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Helper\SecurityHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends Controller
{
    /**
     * @Route("/view/{projectId}{slash}", name="project")
     * @ParamConverter("project", options={"id" = "projectId"})
     * @SecureParam(name="project", permissions="PROJECT_ROLE_DEFAULT")
     */
    public function defaultAction(Project $project)
    {
        $role = $this->get('aerial_ship_steel_mq.manager.project_role')->getProjectRoleForCurrentUser($project);

        return $this->render('@AerialShipSteelMq/project/index.html.twig', array(
            'project' => $project,
            'role' => $role,
        ));
    }

    /**
     * @Route("/ajax/{projectId}/delete/queue/{queueId}/modal{slash}", name="delete_queue_modal")
     * @ParamConverter("project", options={"id" = "projectId"})
     * @ParamConverter("queue", options={"id" = "queueId"})
     * @SecureParam(name="project", permissions="PROJECT_ROLE_QUEUE")
     */
    public function deleteQueueModalAction(Project $project, Queue $queue, Request $request)
    {
        SecurityHelper::checkQueueIsInProject($project, $queue);

        $form = $this->getConfirmForm();

        return $this->render('@AerialShipSteelMq/project/partial/queue_delete_modal.html.twig', array(
            'form' => $form->createView(),
            'queue' => $queue,
        ));
    }

    /**
     * @Route("/ajax/{projectId}/delete/queue/{queueId}/form{slash}", name="delete_queue_form")
     * @ParamConverter("project", options={"id" = "projectId"})
     * @ParamConverter("queue", options={"id" = "queueId"})
     * @SecureParam(name="project", permissions="PROJECT_ROLE_QUEUE")
     */
    public function deleteQueueFormAction(Project $project, Queue $queue, Request $request)
    {
        SecurityHelper::checkQueueIsInProject($project, $queue);

        $form = $this->getConfirmForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->get('aerial_ship_steel_mq.manager.queue')->delete($queue);
            $request->getSession()->getFlashBag()->add(
                'notice',
                sprintf('Queue "%s" has been deleted.', $queue->getTitle())
            );

            return $this->redirect($this->generateUrl('project', array('projectId' => $project->getId())));
        }

        return $this->render('@AerialShipSteelMq/project/partial/queue_delete_form.html.twig', array(
            'form' => $form->createView(),
            'queue' => $queue,
        ));
    }

    /**
     * @Route("/ajax/{projectId}/clear/queue/{queueId}/modal{slash}", name="clear_queue_modal")
     * @ParamConverter("project", options={"id" = "projectId"})
     * @ParamConverter("queue", options={"id" = "queueId"})
     * @SecureParam(name="project", permissions="PROJECT_ROLE_QUEUE")
     */
    public function clearQueueModalAction(Project $project, Queue $queue, Request $request)
    {
        SecurityHelper::checkQueueIsInProject($project, $queue);

        $form = $this->getConfirmForm();

        return $this->render('@AerialShipSteelMq/project/partial/queue_clear_modal.html.twig', array(
            'form' => $form->createView(),
            'queue' => $queue,
        ));
    }

    /**
     * @Route("/ajax/{projectId}/clear/queue/{queueId}/form{slash}", name="clear_queue_form")
     * @ParamConverter("project", options={"id" = "projectId"})
     * @ParamConverter("queue", options={"id" = "queueId"})
     * @SecureParam(name="project", permissions="PROJECT_ROLE_QUEUE")
     */
    public function clearQueueFormAction(Project $project, Queue $queue, Request $request)
    {
        SecurityHelper::checkQueueIsInProject($project, $queue);

        $form = $this->getConfirmForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->get('aerial_ship_steel_mq.manager.queue')->clear($queue);
            $request->getSession()->getFlashBag()->add(
                'notice',
                sprintf('Queue "%s" has been cleared.', $queue->getTitle())
            );

            return $this->redirect($this->generateUrl('project', array('projectId' => $project->getId())));
        }

        return $this->render('@AerialShipSteelMq/project/partial/queue_clear_form.html.twig', array(
            'form' => $form->createView(),
            'queue' => $queue,
        ));
    }

    /**
     * @Route("/ajax/{projectId}/queue/create/modal{slash}", name="create_queue_modal")
     * @ParamConverter("project", options={"id" = "projectId"})
     * @SecureParam(name="project", permissions="PROJECT_ROLE_QUEUE")
     */
    public function createQueueModalAction(Project $project)
    {
        $form = $this->getQueueForm();

        return $this->render('@AerialShipSteelMq/project/partial/queue_create_modal.html.twig', array(
            'form' => $form->createView(),
            'project' => $project,
        ));
    }

    /**
     * @Route("/ajax/{projectId}/queue/create/form{slash}", name="create_queue_form")
     * @ParamConverter("project", options={"id" = "projectId"})
     * @SecureParam(name="project", permissions="PROJECT_ROLE_QUEUE")
     */
    public function createQueueFormAction(Project $project, Request $request)
    {
        $form = $this->getQueueForm();

        $reload = 0;
        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var Queue $queue */
            $queue = $form->getData();
            $this->get('aerial_ship_steel_mq.manager.queue')->create($project, $queue);
            $reload = 100;
            $request->getSession()->getFlashBag()->add(
                'notice',
                sprintf('Queue "%s" has been created.', $queue->getTitle())
            );
        }

        $response = $this->render('@AerialShipSteelMq/project/partial/queue_create_form.html.twig', array(
            'form' => $form->createView(),
            'project' => $project,
        ));

        if ($reload) {
            $response->headers->add(array('X-AS-Reload' => array($reload)));
        }

        return $response;
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    private function getConfirmForm()
    {
        $form = $this->createForm('confirm', null, array(
            'cancel' => false,
        ));

        return $form;
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    private function getQueueForm()
    {
        $queue = new Queue();
        $this->get('aerial_ship_steel_mq.defaulter.queue')->setDefaults($queue);
        $form = $this->createForm('queue', $queue);

        $form->add('actions', 'form_actions', [
            'buttons' => [
                'save' => ['type' => 'submit', 'options' => [
                    'label' => 'Save',
                    'attr' => [
                        'class' => 'pull-right',
                    ],
                ]],
            ]
        ]);

        return $form;
    }
}
