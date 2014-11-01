<?php

namespace AerialShip\SteelMqBundle\Controller\Api;

use AerialShip\SteelMqBundle\Entity\Project;
use JMS\Serializer\SerializationContext;
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

        return $this->handleView(
            $this->view($result)->setSerializationContext(
                SerializationContext::create()->setGroups(array('Default', 'roles'))
            )
        );
    }

    /**
     * @Route("{slash}", name="projects_create")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $project = new Project();
        $form = $this->createForm('project', $project);

        $form->handleRequest($request);
        if (false == $form->isValid()) {
            return $this->handleView($this->view($form, 400));
        }

        $this->get('aerial_ship_steel_mq.manager.project')->create($project, $this->getUser());

        return $this->handleView($this->view(array(
            'id' => $project->getId(),
            'success' => true,
        )));
    }

}
