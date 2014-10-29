<?php

namespace AerialShip\SteelMqBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @param $accessToken
     * @return User|null
     */
    public function getByAccessToken($accessToken)
    {
        return $this->findOneBy(array('accessToken'=>$accessToken));
    }

    /**
     * @param string $username
     * @return null|User
     */
    public function getByUsername($username)
    {
        return $this->findOneBy(array('email'=>$username));
    }
}
