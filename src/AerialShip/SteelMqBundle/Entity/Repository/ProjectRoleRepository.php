<?php

namespace AerialShip\SteelMqBundle\Entity\Repository;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\ProjectRole;
use AerialShip\SteelMqBundle\Entity\User;
use AerialShip\SteelMqBundle\Model\Repository\ProjectRoleRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class ProjectRoleRepository extends EntityRepository implements ProjectRoleRepositoryInterface
{
    /**
     * @param ProjectRole $projectRole
     *
     * @return void
     */
    public function save(ProjectRole $projectRole, $flush = true)
    {
        $this->_em->persist($projectRole);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param ProjectRole $projectRole
     * @param bool        $flush
     *
     * @return void
     */
    public function delete(ProjectRole $projectRole, $flush = true)
    {
        $this->_em->remove($projectRole);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param string $accessToken
     *
     * @return \AerialShip\SteelMqBundle\Entity\ProjectRole|null
     */
    public function getByAccessToken($accessToken)
    {
        return $this->findOneBy(array('accessToken' => $accessToken));
    }

    /**
     * @param array $id [ userId => int, projectId => int ]
     *
     * @return ProjectRole|null
     */
    public function getByUserIdProjectId(array $id)
    {
        return $this->find(array(
            'user' => $id['userId'],
            'project' => $id['projectId'],
        ));
    }

    /**
     * @param User    $user
     * @param Project $project
     *
     * @return null|ProjectRole
     */
    public function getByUserProject(User $user, Project $project)
    {
        return $this->getByUserIdProjectId(array(
            'userId' => $user->getId(),
            'projectId' => $project->getId(),
        ));
    }
}
