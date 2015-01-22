<?php

namespace AerialShip\SteelMqBundle\Services\Manager;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\User;
use AerialShip\SteelMqBundle\Model\Repository\ProjectRoleRepositoryInterface;
use AerialShip\SteelMqBundle\Services\UserProvider;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\SecurityContextInterface;

class ProjectRoleManager
{
    /** @var SecurityContextInterface */
    protected $securityContext;

    /** @var ProjectRoleRepositoryInterface */
    protected $projectRoleRepository;

    /** @var UserProvider */
    protected $userProvider;

    /**
     * @param ProjectRoleRepositoryInterface $projectRoleRepository
     * @param SecurityContextInterface       $securityContext
     * @param UserProvider                   $userProvider
     */
    public function __construct(
        ProjectRoleRepositoryInterface $projectRoleRepository,
        SecurityContextInterface $securityContext,
        UserProvider $userProvider
    ) {
        $this->projectRoleRepository = $projectRoleRepository;
        $this->securityContext = $securityContext;
        $this->userProvider = $userProvider;
    }

    /**
     * @param Project $project
     *
     * @return \AerialShip\SteelMqBundle\Entity\ProjectRole
     */
    public function getProjectRoleForCurrentUser(Project $project)
    {
        $user = $this->userProvider->get();
        $role = $this->projectRoleRepository->getByUserProject($user, $project);
        if (null === $role) {
            throw new AccessDeniedHttpException();
        }

        return $role;
    }
}
