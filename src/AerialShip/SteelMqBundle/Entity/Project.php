<?php

namespace AerialShip\SteelMqBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="AerialShip\SteelMqBundle\Entity\Repository\ProjectRepository")
 * @ORM\Table(name="smq_project")
 * @JMS\ExclusionPolicy("all")
 */
class Project
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank
     * @JMS\Expose
     */
    protected $title;

    /**
     * @var User
     * @ORM\ManyToOne(
     *      targetEntity="User",
     *      inversedBy="ownOProjects"
     * )
     * @ORM\JoinColumn(onDelete="CASCADE")
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
     *      orphanRemoval=true,
     *      cascade={"remove"}
     * )
     */
    protected $projectRoles;

    /**
     * @var Queue[]|ArrayCollection
     * @ORM\OneToMany(
     *      targetEntity="Queue",
     *      mappedBy="project",
     *      orphanRemoval=true,
     *      cascade={"remove"}
     * )
     */
    protected $queues;

    /**
     * @var ProjectRole|null
     * @JMS\Expose
     * @JMS\Accessor(getter="getCurrentRoles")
     * @JMS\Groups({"roles"})
     * @JMS\SerializedName("roles")
     */
    protected $currentProjectRole;

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
    public function getTitle()
    {
        return $this->title;
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
     * @param  \DateTime     $createdAt
     * @return $this|Project
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @param  mixed         $title
     * @return $this|Project
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param  User          $owner
     * @return $this|Project
     */
    public function setOwner(User $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return ProjectRole|null
     */
    public function getCurrentProjectRole()
    {
        return $this->currentProjectRole;
    }

    /**
     * @param  ProjectRole|null $currentProjectRole
     * @return $this|Project
     */
    public function setCurrentProjectRole(ProjectRole $currentProjectRole = null)
    {
        $this->currentProjectRole = $currentProjectRole;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getCurrentRoles()
    {
        if (false == $this->currentProjectRole) {
            return null;
        }

        return ProjectRole::toString($this->currentProjectRole->getRoles());
    }

}
