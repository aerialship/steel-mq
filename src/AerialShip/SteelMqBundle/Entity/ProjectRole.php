<?php

namespace AerialShip\SteelMqBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="smq_project_role")
 */
class ProjectRole
{
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
     * @return mixed
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
        $this->roles = $roles;

        return $this;
    }

}
