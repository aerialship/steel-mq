<?php

namespace AerialShip\SteelMqBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ProjectRoleRepository extends EntityRepository
{
    /**
     * @param string $accessToken
     * @return null|ProjectRole
     */
    public function getByAccessToken($accessToken)
    {
        return $this->findOneBy(array('accessToken'=>$accessToken));
    }
}
