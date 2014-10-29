<?php

namespace AerialShip\SteelMqBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="smq_project")
 */
class Project implements \JsonSerializable
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
     * @param \DateTime|null $createdAt
     * @return $this|Project
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @param mixed $title
     * @return $this|Project
     */
    public function setTitle($title)
    {
        $this->title = $title;

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

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return array(
            'id' => $this->getId(),
            'title' => $this->getTitle(),
        );
    }

}
