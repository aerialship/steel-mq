<?php

namespace AerialShip\SteelMqBundle\Controller\Api;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\ProjectRole;
use AerialShip\SteelMqBundle\Entity\User;
use AerialShip\SteelMqBundle\Helper\RandomHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * @Route("/", name="projects_create")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $json = json_decode($request->getContent(), true);

        if (false == is_array($json)) {
            throw new BadRequestHttpException('Body parameters not supplied');
        }

        $project = (new Project())
            ->setTitle(trim(@$json['title']))
            ->setOwner($this->getUser());
        $projectRole = (new ProjectRole())
            ->setRoles(array(ProjectRole::PROJECT_ROLE_OWNER))
            ->setAccessToken(RandomHelper::generateToken())
            ->setProject($project)
            ->setUser($this->getUser());

        $errors = $this->get('validator')->validate($project);
        if (count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        $em = $this->getEntityManager();

        $em->transactional(function () use ($em, $project, $projectRole) {
            $em->persist($project);
            $em->flush();
            $em->persist($projectRole);
            $em->flush();
        });

        return new JsonResponse(array(
            'id' => $project->getId(),
            'success' => true,
        ));
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return parent::getUser();
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }
}
