<?php

namespace AerialShip\SteelMqBundle\Controller\Html;

use AerialShip\SteelMqBundle\Entity\ProjectRole;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('@AerialShipSteelMq/default/index.html.twig', array('name' => 'pera'));
    }

    /**
     * @Route("/ajax/credentials/{userId}/{projectId}",
     *      name="project_credentials",
     *      requirements={"userId" = "\d+", "projectId" = "\d+"}
     * )
     * @ParamConverter("role", options={"id" = {"userId", "projectId"}, "repository_method" = "getByUserIdProjectId"})
     * @SecureParam(name="role", permissions="PROJECT_ROLE_DEFAULT")
     */
    public function credentialsAction(ProjectRole $role)
    {
        return $this->render('@AerialShipSteelMq/default/partial/credentials_modal.html.twig', array(
            'role' => $role,
        ));
    }

    /**
     * @Route("/ajax/settings/{userId}/{projectId}/modal",
     *      name="project_settings_modal",
     *      requirements={"userId" = "\d+", "projectId" = "\d+"}
     * )
     * @ParamConverter("role", options={"id" = {"userId", "projectId"}, "repository_method" = "getByUserIdProjectId"})
     * @SecureParam(name="role", permissions="PROJECT_ROLE_OWNER")
     */
    public function settingsModalAction(ProjectRole $role)
    {
        return $this->render('@AerialShipSteelMq/default/partial/settings_modal.html.twig', array(
            'role' => $role,
        ));
    }

    /**
     * @Route("/ajax/settings/{userId}/{projectId}/form",
     *      name="project_settings_form",
     *      requirements={"userId" = "\d+", "projectId" = "\d+"}
     * )
     * @ParamConverter("role", options={"id" = {"userId", "projectId"}, "repository_method" = "getByUserIdProjectId"})
     * @SecureParam(name="role", permissions="PROJECT_ROLE_OWNER")
     */
    public function settingsFormAction(ProjectRole $role, Request $request)
    {
        $form = $this->createForm('project', $role->getProject(), array(
            'csrf_protection' => true,
        ));

        $msg = '';
        $msgType = 'success';
        $close = false;

        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($request->request->get('save')) {
                $this->get('aerial_ship_steel_mq.manager.project')->save($form->getData());
                $msg = 'Project updated';
                $close = true;
            } elseif ($request->request->get('delete')) {
                $msg = 'Not implemented';
                $msgType = 'warning';
            } else {
                throw new BadRequestHttpException();
            }
        }

        return $this->render('@AerialShipSteelMq/default/partial/settings_form.html.twig', array(
            'role' => $role,
            'form' => $form->createView(),
            'msg' => $msg,
            'msg_type' => $msgType,
            'close' => $close,
        ));
    }

    /**
     * @Route("/ajax/share/{userId}/{projectId}/modal",
     *      name="project_share_modal",
     *      requirements={"userId" = "\d+", "projectId" = "\d+"}
     * )
     * @ParamConverter("role", options={"id" = {"userId", "projectId"}, "repository_method" = "getByUserIdProjectId"})
     * @SecureParam(name="role", permissions="PROJECT_ROLE_OWNER")
     */
    public function shareModalAction(ProjectRole $role)
    {
        return $this->render('@AerialShipSteelMq/default/partial/share_modal.html.twig', array(
            'role' => $role,
        ));
    }

    /**
     * @Route("/ajax/share/{userId}/{projectId}/form",
     *      name="project_share_form",
     *      requirements={"userId" = "\d+", "projectId" = "\d+"}
     * )
     * @ParamConverter("role", options={"id" = {"userId", "projectId"}, "repository_method" = "getByUserIdProjectId"})
     * @SecureParam(name="role", permissions="PROJECT_ROLE_SHARE")
     */
    public function shareFormAction(ProjectRole $role, Request $request)
    {
        list($owners, $data) = $this->getProjectShareService()->getShareInfo($role->getProject());

        $form = $this->createForm('share_project', $data);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $this->getProjectShareService()->setSharing($role->getProject(), $data['roles']);
        }

        return $this->render('@AerialShipSteelMq/default/partial/share_form.html.twig', array(
            'role' => $role,
            'form' => $form->createView(),
            'owners' => $owners,
        ));
    }

    /**
     * @return \AerialShip\SteelMqBundle\Services\Sharing\ProjectShareService
     */
    private function getProjectShareService()
    {
        return $this->get('aerial_ship_steel_mq.model.project_share_service');
    }
}
