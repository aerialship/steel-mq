<?php

namespace AerialShip\SteelMqBundle\Services\Manager;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\ProjectRole;
use AerialShip\SteelMqBundle\Entity\User;
use AerialShip\SteelMqBundle\Helper\TokenHelper;
use AerialShip\SteelMqBundle\Model\Repository\ProjectRepositoryInterface;
use AerialShip\SteelMqBundle\Model\Repository\ProjectRoleRepositoryInterface;
use AerialShip\SteelMqBundle\Services\UserProvider;
use Symfony\Component\Security\Core\SecurityContextInterface;

class ProjectManager
{
    /** @var SecurityContextInterface */
    protected $securityContext;

    /** @var ProjectRepositoryInterface */
    protected $projectRepository;

    /** @var ProjectRoleRepositoryInterface */
    protected $projectRoleRepository;

    /** @var UserProvider */
    protected $userProvider;

    /**
     * @param ProjectRepositoryInterface     $projectRepository
     * @param ProjectRoleRepositoryInterface $projectRoleRepository
     * @param SecurityContextInterface       $securityContext
     * @param UserProvider                   $userProvider
     */
    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        ProjectRoleRepositoryInterface $projectRoleRepository,
        SecurityContextInterface $securityContext,
        UserProvider $userProvider
    ) {
        $this->projectRepository = $projectRepository;
        $this->projectRoleRepository = $projectRoleRepository;
        $this->securityContext = $securityContext;
        $this->userProvider = $userProvider;
    }

    /**
     * @param  User|null $user
     * @param  bool      $security
     * @return Project[]
     */
    public function getList(User $user = null, $security = true)
    {
        if (false === $user) {
            $user = $this->userProvider->get();
        }

        $result = array();
        foreach ($user->getProjectRoles() as $projectRole) {
            if (false === $security ||
                $this->securityContext->isGranted(ProjectRole::PROJECT_ROLE_DEFAULT, $projectRole->getProject())
            ) {
                $projectRole->getProject()->setCurrentProjectRole($projectRole);
                $result[] = $projectRole->getProject();
            }
        }

        return $result;
    }

    /**
     * @param  Project     $project
     * @param  User|null   $owner
     * @return ProjectRole
     */
    public function create(Project $project, User $owner = null)
    {
        if ($project->getId()) {
            throw new \InvalidArgumentException('Project already created');
        }

        if (false === $owner) {
            $owner = $this->userProvider->get();
        }

        $project->setOwner($owner);
        $projectRole = (new ProjectRole())
            ->setRoles(array(ProjectRole::PROJECT_ROLE_OWNER))
            ->setAccessToken(TokenHelper::generate())
            ->setProject($project)
            ->setUser($owner);

        $this->projectRepository->transactional(function () use ($project, $projectRole) {
            $this->projectRepository->save($project);
            $this->projectRoleRepository->save($projectRole);
        });

        return $projectRole;
    }
}
