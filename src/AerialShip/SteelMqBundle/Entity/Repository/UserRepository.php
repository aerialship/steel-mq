<?php

namespace AerialShip\SteelMqBundle\Entity\Repository;

use AerialShip\SteelMqBundle\Model\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * @param  string                                     $accessToken
     * @return \AerialShip\SteelMqBundle\Entity\User|null
     */
    public function getByAccessToken($accessToken)
    {
        return $this->findOneBy(array('accessToken'=>$accessToken));
    }

    /**
     * @param  int                                        $userId
     * @return \AerialShip\SteelMqBundle\Entity\User|null
     */
    public function getById($userId)
    {
        return $this->find($userId);
    }

    /**
     * @param  string                                     $username
     * @return \AerialShip\SteelMqBundle\Entity\User|null
     */
    public function getByUsername($username)
    {
        return $this->findOneBy(array('email'=>$username));
    }
}
