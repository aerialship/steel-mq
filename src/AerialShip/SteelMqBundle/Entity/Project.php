<?php

namespace AerialShip\SteelMqBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="smq_project")
 */
class Project 
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=60)
     */
    protected $name;

    /**
     * @var User
     * @ORM\ManyToOne(
     *      targetEntity="User",
     *      inversedBy="ownOProjects"
     * )
     */
    protected $owner;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var ProjectRole[]|ArrayCollection
     * @ORM\OneToMany(
     *      targetEntity="ProjectRole",
     *      mappedBy="project",
     *      orphanRemoval=true
     * )
     */
    protected $projectRoles;

    /**
     * @var Queue[]|ArrayCollection
     * @ORM\OneToMany(
     *      targetEntity="Queue",
     *      mappedBy="project",
     *      orphanRemoval=true
     * )
     */
    protected $queues;

    /**
     *
     */
    public function __construct()
    {
        $this->projectRoles = new ArrayCollection();
        $this->queues = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return ProjectRole[]|ArrayCollection
     */
    public function getProjectRoles()
    {
        return $this->projectRoles;
    }

    /**
     * @return Queue[]|ArrayCollection
     */
    public function getQueues()
    {
        return $this->queues;
    }

    /**
     * @param \DateTime|null $createdAt
     * @return $this|Project
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @param mixed $name
     * @return $this|Project
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param User $owner
     * @return $this|Project
     */
    public function setOwner(User $owner)
    {
        $this->owner = $owner;
        return $this;
    }

}
