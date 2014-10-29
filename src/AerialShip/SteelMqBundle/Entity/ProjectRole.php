<?php

namespace AerialShip\SteelMqBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AerialShip\SteelMqBundle\Entity\ProjectRoleRepository")
 * @ORM\Table(name="smq_project_role")
 */
class ProjectRole
{
    const PROJECT_ROLE_DEFAULT = 'PROJECT_ROLE_DEFAULT';
    const PROJECT_ROLE_QUEUE = 'PROJECT_ROLE_QUEUE';
    const PROJECT_ROLE_SHARE = 'PROJECT_ROLE_SHARE';
    const PROJECT_ROLE_SUBSCRIBE = 'PROJECT_ROLE_SUBSCRIBE';

    private static $validRoles = array(
        self::PROJECT_ROLE_DEFAULT => 1,
        self::PROJECT_ROLE_QUEUE => 1,
        self::PROJECT_ROLE_SHARE => 1,
        self::PROJECT_ROLE_SUBSCRIBE => 1,
    );

    /**
     * @var User
     * @ORM\Id
     * @ORM\ManyToOne(
     *      targetEntity="User",
     *      inversedBy="projectRoles"
     * )
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $user;

    /**
     * @var Project
     * @ORM\Id
     * @ORM\ManyToOne(
     *      targetEntity="Project",
     *      inversedBy="projectRoles"
     * )
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $project;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    protected $roles = array();

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    protected $accessToken;

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return $this|ProjectRole
     */
    public function setRoles(array $roles)
    {
        $this->roles = array();

        foreach ($roles as $role) {
            if (isset(self::$validRoles[$role])) {
                $this->roles[] = $role;
            } else {
                throw new \InvalidArgumentException(sprintf("Invalid project role '%s'", $role));
            }
        }

        return $this;
    }

    /**
     * @param string $role
     * @return $this|ProjectRole
     */
    public function addRole($role)
    {
        $role = strtoupper($role);

        if (false == isset(self::$validRoles[$role])) {
            throw new \InvalidArgumentException(sprintf("Invalid project role '%s'", $role));
        }

        if (false == in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @param string $role
     * @return $this|ProjectRole
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * @param string $role
     * @return bool
     */
    public static function isRoleValid($role)
    {
        return isset(self::$validRoles[$role]);
    }

    public static function toStrings(array $roles)
    {
        $result = array();
        foreach ($roles as $role) {
            if (self::isRoleValid($role)) {
                $result[] = strtolower(substr($role, 13));
            }
        }

        return array_unique($result);
    }

    public static function fromString(array $roles)
    {
        $result = array();
        foreach ($roles as $role) {
            $role = 'PROJECT_ROLE_'.strtoupper($role);
            if (self::isRoleValid($role)) {
                $result[] = $role;
            }
        }

        return array_unique($result);
    }
}
