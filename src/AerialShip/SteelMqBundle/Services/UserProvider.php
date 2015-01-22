<?php

namespace AerialShip\SteelMqBundle\Services;

use AerialShip\SteelMqBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;

class UserProvider
{
    /** @var  SecurityContextInterface */
    protected $securityContext;

    /**
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @return User
     */
    public function get()
    {
        $token = $this->securityContext->getToken();
        if (false === $token) {
            throw new AccessDeniedException();
        }

        $user = $token->getUser();
        if (false === $user instanceof User) {
            throw new \LogicException();
        }

        return $user;
    }
}
