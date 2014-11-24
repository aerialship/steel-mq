<?php

namespace AerialShip\SteelMqBundle\Controller\Api;

use AerialShip\SteelMqBundle\Helper\CastHelper;
use AerialShip\SteelMqBundle\Helper\RequestHelper;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/projects")
 */
class ProjectController extends AbstractApiController
{
    /**
     * @Route("{slash}", name="projects_list")
     * @Method({"GET"})
     */
    public function listAction()
    {
        $result = $this->get('aerial_ship_steel_mq.manager.project')->getList();

        return $this->handleData($result, array('Default', 'roles'));
    }

    /**
     * @Route("{slash}", name="projects_create")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        RequestHelper::ensure($request->request, array('project' => array()));

        $form = $this->createForm('project');
        $form->handleRequest($request);
        if (false === $form->isValid()) {
            return $this->handleView($this->view($form, 400));
        }

        $project = CastHelper::asProject($form->getData());

        $this->get('aerial_ship_steel_mq.manager.project')->create(
            $project,
            CastHelper::asUser($this->getUser())
        );

        return $this->handleData($this->getSuccessData(array('id' => $project->getId())));
    }
}
