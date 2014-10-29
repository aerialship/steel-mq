<?php

namespace AerialShip\SteelMqBundle\Controller\Api;

use AerialShip\SteelMqBundle\Entity\ProjectRole;
use AerialShip\SteelMqBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/projects")
 */
class ProjectController extends Controller
{
    /**
     * @Route("/", name="projects_list")
     * @Method({"GET"})
     */
    public function listAction()
    {
        $sc = $this->get('security.context');
        $result = array();
        foreach ($this->getUser()->getProjectRoles() as $projectRole) {
            if ($sc->isGranted(ProjectRole::PROJECT_ROLE_DEFAULT, $projectRole->getProject())) {
                $obj = $projectRole->getProject()->jsonSerialize();
                $obj['roles'] = ProjectRole::toStrings($projectRole->getRoles());
                $result[] = $obj;
            }
        }

        return new JsonResponse($result);
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return parent::getUser();
    }

}
