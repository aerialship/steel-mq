<?php

namespace AerialShip\SteelMqBundle\Controller\Html;

use AerialShip\SteelMqBundle\Entity\Message;
use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Helper\SecurityHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class QueueController extends Controller
{
    /**
     * @Route("/view/{projectId}/queue/{queueId}/messages{slash}",
     *      name="messages",
     *      requirements={"projectId" = "\d+", "queueId" = "\d+"}
     * )
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

    /**
     * @Route("/view/{projectId}/queue/{queueId}/messages/{messageId}/modal",
     *      name="view_message_modal",
     *      requirements={"projectId" = "\d+", "queueId" = "\d+", "messageId" = "\d+"}
     * )
     * @ParamConverter("project", options={"id" = "projectId"})
     * @ParamConverter("queue", options={"id" = "queueId"})
     * @ParamConverter("message", options={"id" = "messageId"})
     * @SecureParam(name="project", permissions="PROJECT_ROLE_DEFAULT")
     */
    public function viewMessageModalAction(Project $project, Queue $queue, Message $message)
    {
        SecurityHelper::checkQueueIsInProject($project, $queue);
        SecurityHelper::checkMessageIsInQueue($queue, $message);

        $body = $message->getBody();
        $json = json_decode($body);
        if (json_last_error() == JSON_ERROR_NONE) {
            $body = json_encode($json, JSON_PRETTY_PRINT);
        }

        return $this->render('@AerialShipSteelMq/queue/partial/view_modal.html.twig', array(
            'message' => $message,
            'body' => $body,
        ));
    }

    /**
     * @Route("/view/{projectId}/queue/{queueId}/messages/{messageId}/download",
     *      name="download_message",
     *      requirements={"projectId" = "\d+", "queueId" = "\d+", "messageId" = "\d+"}
     * )
     * @ParamConverter("project", options={"id" = "projectId"})
     * @ParamConverter("queue", options={"id" = "queueId"})
     * @ParamConverter("message", options={"id" = "messageId"})
     * @SecureParam(name="project", permissions="PROJECT_ROLE_DEFAULT")
     */
    public function downloadMessageAction(Project $project, Queue $queue, Message $message)
    {
        SecurityHelper::checkQueueIsInProject($project, $queue);
        SecurityHelper::checkMessageIsInQueue($queue, $message);

        $response = new Response($message->getBody());
        $d = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $message->getId().'.txt'
        );
        $response->headers->set('Content-Disposition', $d);
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }

    /**
     * @Route("/view/{projectId}/queue/{queueId}/messages/{messageId}/delete/modal",
     *      name="delete_message_modal",
     *      requirements={"projectId" = "\d+", "queueId" = "\d+", "messageId" = "\d+"}
     * )
     * @ParamConverter("project", options={"id" = "projectId"})
     * @ParamConverter("queue", options={"id" = "queueId"})
     * @ParamConverter("message", options={"id" = "messageId"})
     * @SecureParam(name="project", permissions="PROJECT_ROLE_DEFAULT")
     */
    public function deleteMessageModalAction(Project $project, Queue $queue, Message $message)
    {
        SecurityHelper::checkQueueIsInProject($project, $queue);
        SecurityHelper::checkMessageIsInQueue($queue, $message);

        $form = $this->getConfirmForm();

        return $this->render('@AerialShipSteelMq/queue/partial/message_delete_modal.html.twig', array(
            'form' => $form->createView(),
            'message' => $message,
        ));
    }

    /**
     * @Route("/view/{projectId}/queue/{queueId}/messages/{messageId}/delete/form",
     *      name="delete_message_form",
     *      requirements={"projectId" = "\d+", "queueId" = "\d+", "messageId" = "\d+"}
     * )
     * @ParamConverter("project", options={"id" = "projectId"})
     * @ParamConverter("queue", options={"id" = "queueId"})
     * @ParamConverter("message", options={"id" = "messageId"})
     * @SecureParam(name="project", permissions="PROJECT_ROLE_DEFAULT")
     */
    public function deleteMessageFormAction(Project $project, Queue $queue, Message $message, Request $request)
    {
        SecurityHelper::checkQueueIsInProject($project, $queue);
        SecurityHelper::checkMessageIsInQueue($queue, $message);

        $form = $this->getConfirmForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $id = $message->getId();
            $this->get('aerial_ship_steel_mq.manager.message')->delete($message);

            $request->getSession()->getFlashBag()->add(
                'notice',
                sprintf('Message #%s has been deleted.', $id)
            );

            return $this->redirect($this->generateUrl('messages', array(
                'projectId' => $project->getId(),
                'queueId' => $queue->getId(),
            )));
        }

        return $this->render('@AerialShipSteelMq/queue/partial/message_delete_form.html.twig', array(
            'form' => $form->createView(),
            'message' => $message,
        ));
    }

    /**
     * @Route("/view/{projectId}/queue/{queueId}/messages/create/modal",
     *      name="create_message_modal",
     *      requirements={"projectId" = "\d+", "queueId" = "\d+"}
     * )
     * @ParamConverter("project", options={"id" = "projectId"})
     * @ParamConverter("queue", options={"id" = "queueId"})
     * @SecureParam(name="project", permissions="PROJECT_ROLE_DEFAULT")
     */
    public function createMessageModalAction(Project $project, Queue $queue)
    {
        SecurityHelper::checkQueueIsInProject($project, $queue);

        $form = $this->getMessageForm($queue);

        return $this->render('@AerialShipSteelMq/queue/partial/message_create_modal.html.twig', array(
            'form' => $form->createView(),
            'queue' => $queue,
        ));
    }

    /**
     * @Route("/view/{projectId}/queue/{queueId}/messages/create/form",
     *      name="create_message_form",
     *      requirements={"projectId" = "\d+", "queueId" = "\d+"}
     * )
     * @ParamConverter("project", options={"id" = "projectId"})
     * @ParamConverter("queue", options={"id" = "queueId"})
     * @SecureParam(name="project", permissions="PROJECT_ROLE_DEFAULT")
     */
    public function createMessageFormAction(Project $project, Queue $queue, Request $request)
    {
        SecurityHelper::checkQueueIsInProject($project, $queue);

        $reload = false;
        $form = $this->getMessageForm($queue);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->get('aerial_ship_steel_mq.manager.message')->add($queue, array('messages' => array($form->getData())));
            $reload = 10;
        }

        $response = $this->render('@AerialShipSteelMq/queue/partial/message_create_form.html.twig', array(
            'form' => $form->createView(),
            'queue' => $queue,
        ));

        if ($reload) {
            $response->headers->set('X-AS-Reload', $reload);
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
    private function getMessageForm(Queue $queue)
    {
        $message = new Message();
        $message->setQueue($queue);
        $this->get('aerial_ship_steel_mq.defaulter.message')->setDefaults($message);

        $form = $this->createForm('message', $message);

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
