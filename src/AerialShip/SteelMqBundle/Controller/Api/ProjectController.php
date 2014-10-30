<?php

namespace AerialShip\SteelMqBundle\Controller\Api;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\ProjectRole;
use AerialShip\SteelMqBundle\Helper\Helper;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/projects{slash}")
 */
class ProjectController extends AbstractApiController
{
    /**
     * @Route("", name="projects_list")
     * @Method({"GET"})
     */
    public function listAction()
    {
        $sc = $this->get('security.context');
        $result = array();
        foreach ($this->getUser()->getProjectRoles() as $projectRole) {
            if ($sc->isGranted(ProjectRole::PROJECT_ROLE_DEFAULT, $projectRole->getProject())) {
                $projectRole->getProject()->setCurrentProjectRole($projectRole);
                $result[] = $projectRole->getProject();
            }
        }

        return $this->handleView(
            $this->view($result)->setSerializationContext(
                SerializationContext::create()->setGroups(array('Default', 'roles'))
            )
        );
    }

    /**
     * @Route("", name="projects_create")
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

        $project->setOwner($this->getUser());
        $projectRole = (new ProjectRole())
            ->setRoles(array(ProjectRole::PROJECT_ROLE_OWNER))
            ->setAccessToken(Helper::generateToken())
            ->setProject($project)
            ->setUser($this->getUser());

        $em = $this->getEntityManager();

        $em->transactional(function () use ($em, $project, $projectRole) {
            $em->persist($project);
            $em->flush();
            $em->persist($projectRole);
            $em->flush();
        });

        return $this->handleView($this->view(array(
            'id' => $project->getId(),
            'success' => true,
        )));
    }

}
