<?php

namespace AerialShip\SteelMqBundle\Services\Sharing;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\ProjectRole;
use AerialShip\SteelMqBundle\Entity\User;
use AerialShip\SteelMqBundle\Model\Repository\ProjectRoleRepositoryInterface;
use AerialShip\SteelMqBundle\Model\Repository\UserRepositoryInterface;

class ProjectShareService
{
    /** @var ProjectRoleRepositoryInterface */
    protected $projectRoleRepository;

    /** @var  UserRepositoryInterface */
    protected $userRepository;

    /**
     * @param ProjectRoleRepositoryInterface $projectRoleRepository
     * @param UserRepositoryInterface        $userRepository
     */
    public function __construct(ProjectRoleRepositoryInterface $projectRoleRepository, UserRepositoryInterface $userRepository)
    {
        $this->projectRoleRepository = $projectRoleRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Project $project
     *
     * @return array [
     *               0 => ProjectRole[]                  array of project owner entities
     *               1 => [string email, array roles]    array of project collaborators
     *               ]
     */
    public function getShareInfo(Project $project)
    {
        $owners = array();
        $data = array('roles' => array());
        foreach ($project->getProjectRoles() as $role) {
            if ($role->hasRole(ProjectRole::PROJECT_ROLE_OWNER)) {
                $owners[] = $role;
            } else {
                $data['roles'][] = array(
                    'email' => $role->getUser()->getEmail(),
                    'roles' => $role->getRoles(),
                );
            }
        }

        return array($owners, $data);
    }

    /**
     * @param Project $project
     * @param array   $data    array of arrays with email and roles, as return value of getShareInfo()
     */
    public function setSharing(Project $project, array $data)
    {
        $dataIndex = array();
        foreach ($data as $collaborator) {
            $dataIndex[$collaborator['email']] = $collaborator['roles'];
        }

        $roleIndex = array();

        foreach ($project->getProjectRoles() as $role) {
            if ($role->hasRole(ProjectRole::PROJECT_ROLE_OWNER)) {
                continue;
            }

            $roleIndex[$role->getUser()->getEmail()] = 1;

            if (isset($dataIndex[$role->getUser()->getEmail()])) {
                $role->setRoles($dataIndex[$role->getUser()->getEmail()]);
                $role->addRole(ProjectRole::PROJECT_ROLE_DEFAULT);
                $this->projectRoleRepository->save($role);
            } else {
                $project->getProjectRoles()->removeElement($role);
                $this->projectRoleRepository->delete($role);
            }
        }

        foreach ($data as $collaborator) {
            if (false == isset($roleIndex[$collaborator['email']])) {
                $email = $collaborator['email'];
                $role = new ProjectRole();
                $role->setUser($this->getUserByEmail($email))
                    ->setProject($project)
                    ->setAccessToken(substr(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36), 18))
                    ->setRoles($collaborator['roles'])
                    ->addRole(ProjectRole::PROJECT_ROLE_DEFAULT)
                ;
                $this->projectRoleRepository->save($role);
                $project->getProjectRoles()->add($role);
                $role->getUser()->getProjectRoles()->add($role);
            }
        }
    }

    /**
     * @param string $email
     *
     * @return User
     */
    protected function getUserByEmail($email)
    {
        $user = $this->userRepository->getByUsername($email);
        if (false == $user) {
            $user = $this->createUser($email);
        }

        return $user;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    protected function createUser($email)
    {
        $user = new User();
        $user->setEmail($email)
            ->setName($email)
            ->setPlainPassword(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36))
            ->setAccessToken(substr(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36), 18))
            ->setPasswordToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36))
            ->setPasswordRequestAt(new \DateTime())
            ->setCreatedAt(new \DateTime())
        ;
        $this->userRepository->save($user);

        return $user;
    }
}
