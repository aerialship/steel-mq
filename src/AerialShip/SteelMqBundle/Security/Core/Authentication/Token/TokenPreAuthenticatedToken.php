<?php

namespace AerialShip\SteelMqBundle\Security\Core\Authentication\Token;

use AerialShip\SteelMqBundle\Entity\Project;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;

class TokenPreAuthenticatedToken extends PreAuthenticatedToken
{
    /**
     * @var Project|null
     */
    protected $project;

    /**
     * @param $user
     * @param Project $project
     * @param $credentials
     * @param array   $providerKey
     * @param array   $roles
     */
    public function __construct($user, Project $project = null, $credentials, $providerKey, array $roles = array())
    {
        parent::__construct($user, $credentials, $providerKey, $roles);

        $this->project = $project;
    }

    /**
     * @return Project|null
     */
    public function getProject()
    {
        return $this->project;
    }
}
