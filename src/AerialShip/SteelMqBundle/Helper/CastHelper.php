<?php

namespace AerialShip\SteelMqBundle\Helper;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\User;

final class CastHelper
{
    /**
     * @param $user
     * @return User
     */
    public static function asUser($user)
    {
        if ($user instanceof User) {
            return $user;
        }

        throw new \InvalidArgumentException('Expected User');
    }

    /**
     * @param $project
     * @return Project
     */
    public static function asProject($project)
    {
        if ($project instanceof Project) {
            return $project;
        }

        throw new \InvalidArgumentException('Expected Project');
    }
}
