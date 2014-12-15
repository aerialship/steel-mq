<?php

namespace AerialShip\SteelMqBundle\Model\Repository;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\ProjectRole;
use AerialShip\SteelMqBundle\Entity\User;

interface ProjectRoleRepositoryInterface
{
    /**
     * @param  ProjectRole $projectRole
     * @param  bool        $flush
     * @return void
     */
    public function save(ProjectRole $projectRole, $flush = true);

    /**
     * @param ProjectRole $projectRole
     * @param bool        $flush
     *
     * @return void
     */
    public function delete(ProjectRole $projectRole, $flush = true);

    /**
     * @param  string           $accessToken
     * @return null|ProjectRole
     */
    public function getByAccessToken($accessToken);

    /**
     * @param array $id
     *
     * @return ProjectRole|null
     */
    public function getByUserIdProjectId(array $id);

    /**
     * @param User    $user
     * @param Project $project
     *
     * @return null|ProjectRole
     */
    public function getByUserProject(User $user, Project $project);
}
