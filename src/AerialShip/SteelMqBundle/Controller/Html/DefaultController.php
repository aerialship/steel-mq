<?php

namespace AerialShip\SteelMqBundle\Controller\Html;

use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;

class DefaultController extends FOSRestController
{
    /**
     * @Route("/", defaults={ "_format"="json" }, name="homepage")
     * @Rest\View
     */
    public function indexAction()
    {
        $projects = $this->getDoctrine()->getManager()->getRepository('AerialShipSteelMqBundle:Project')->findBy(array());
        foreach ($projects as $project) {
            foreach ($project->getProjectRoles() as $role) {
                $project->setCurrentProjectRole($role);
                break;
            }
        }

        $ctx = SerializationContext::create()
            ->setGroups(array('Default', 'roles'));

        $view = $this->view(array("projects"=>$projects))
            ->setSerializationContext($ctx);

        return $this->handleView($view);
    }
}
