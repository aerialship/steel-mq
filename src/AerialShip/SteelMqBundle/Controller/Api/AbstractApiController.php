<?php

namespace AerialShip\SteelMqBundle\Controller\Api;

use AerialShip\SteelMqBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;

class AbstractApiController extends FOSRestController
{
    /**
     * @return User
     */
    public function getUser()
    {
        return parent::getUser();
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

}
