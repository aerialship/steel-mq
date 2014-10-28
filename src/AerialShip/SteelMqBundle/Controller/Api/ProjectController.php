<?php

namespace AerialShip\SteelMqBundle\Controller\Api;

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
        return new JsonResponse(array($this->getUser()->getUsername(), $this->getUser()->getRoles()));
    }
}
