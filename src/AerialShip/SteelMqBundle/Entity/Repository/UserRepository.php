<?php

namespace AerialShip\SteelMqBundle\Entity\Repository;

use AerialShip\SteelMqBundle\Entity\User;
use AerialShip\SteelMqBundle\Model\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * @param  User $user
     * @param  bool $flush
     * @return void
     */
    public function save(User $user, $flush = true)
    {
        $this->_em->persist($user);
        if ($flush) {
            $this->_em->flush($user);
        }
    }

    /**
     * @param  string                                     $accessToken
     * @return \AerialShip\SteelMqBundle\Entity\User|null
     */
    public function getByAccessToken($accessToken)
    {
        return $this->findOneBy(array('accessToken' => $accessToken));
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
        return $this->findOneBy(array('email' => $username));
    }
}
