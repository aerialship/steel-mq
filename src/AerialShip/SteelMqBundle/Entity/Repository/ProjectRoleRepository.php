<?php

namespace AerialShip\SteelMqBundle\Entity\Repository;

use AerialShip\SteelMqBundle\Entity\ProjectRole;
use AerialShip\SteelMqBundle\Model\Repository\ProjectRoleRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class ProjectRoleRepository extends EntityRepository implements ProjectRoleRepositoryInterface
{
    /**
     * @param ProjectRole $projectRole
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
     * @param string $accessToken
     * @return \AerialShip\SteelMqBundle\Entity\ProjectRole|null
     */
    public function getByAccessToken($accessToken)
    {
        return $this->findOneBy(array('accessToken'=>$accessToken));
    }

}
