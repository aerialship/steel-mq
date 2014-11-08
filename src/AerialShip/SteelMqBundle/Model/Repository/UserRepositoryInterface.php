<?php

namespace AerialShip\SteelMqBundle\Model\Repository;

use AerialShip\SteelMqBundle\Entity\User;

interface UserRepositoryInterface
{
    /**
     * @param  string    $accessToken
     * @return User|null
     */
    public function getByAccessToken($accessToken);

    /**
     * @param  int                                        $userId
     * @return \AerialShip\SteelMqBundle\Entity\User|null
     */
    public function getById($userId);

    /**
     * @param  string    $username
     * @return null|User
     */
    public function getByUsername($username);

}
