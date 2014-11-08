<?php

namespace AerialShip\SteelMqBundle\Model\Repository;

use AerialShip\SteelMqBundle\Entity\ProjectRole;

interface ProjectRoleRepositoryInterface
{
    /**
     * @param  ProjectRole $projectRole
     * @param  bool        $flush
     * @return void
     */
    public function save(ProjectRole $projectRole, $flush = true);

    /**
     * @param  string           $accessToken
     * @return null|ProjectRole
     */
    public function getByAccessToken($accessToken);

}
