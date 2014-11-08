<?php

namespace AerialShip\SteelMqBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="AerialShip\SteelMqBundle\Entity\Repository\QueueRepository")
 * @ORM\Table(name="smq_queue")
 * @JMS\ExclusionPolicy("all")
 */
class Queue
{
    const PUSH_TYPE_PULL = 'pull';
    const PUSH_TYPE_UNICAST = 'unicast';
    const PUSH_TYPE_MULTICAST = 'multicast';

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
     * @ORM\Column(type="string", length=100)
     * @JMS\Expose
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(type="string", length=20, name="push_type")
     * @JMS\Expose
     */
    protected $pushType = self::PUSH_TYPE_PULL;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $retries;

    /**
     * @var int
     * @ORM\Column(type="integer", name="retries_delay")
     * @JMS\Expose
     */
    protected $retriesDelay;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose
     */
    protected $errorQueue;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $timeout;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $delay;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $expiresIn;

    /**
     * @var Project
     * @ORM\ManyToOne(
     *      targetEntity="Project",
     *      inversedBy="queues"
     * )
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $project;

    /**
     * @var Subscriber[]|ArrayCollection
     * @ORM\OneToMany(
     *      targetEntity="Subscriber",
     *      mappedBy="queue",
     *      orphanRemoval=true,
     *      cascade={"remove"}
     * )
     */
    protected $subscribers;

    /**
     * @var Message[]|ArrayCollection
     * @ORM\OneToMany(
     *      targetEntity="Message",
     *      mappedBy="queue",
     *      orphanRemoval=true,
     *      cascade={"remove"},
     *      fetch="EXTRA_LAZY"
     * )
     */
    protected $messages;

    /**
     *
     */
    public function __construct()
    {
        $this->subscribers = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    /**
     * @return null|int
     */
    public function getErrorQueue()
    {
        return $this->errorQueue;
    }

    /**
     * @param  null|int    $errorQueue
     * @return $this|Queue
     */
    public function setErrorQueue($errorQueue)
    {
        $this->errorQueue = $errorQueue;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPushType()
    {
        return $this->pushType;
    }

    /**
     * @param  string      $pushType
     * @return $this|Queue
     */
    public function setPushType($pushType)
    {
        $this->pushType = $pushType;

        return $this;
    }

    /**
     * @return int
     */
    public function getRetries()
    {
        return $this->retries;
    }

    /**
     * @param  int         $retries
     * @return $this|Queue
     */
    public function setRetries($retries)
    {
        $this->retries = $retries;

        return $this;
    }

    /**
     * @return int
     */
    public function getRetriesDelay()
    {
        return $this->retriesDelay;
    }

    /**
     * @param  int         $retriesDelay
     * @return $this|Queue
     */
    public function setRetriesDelay($retriesDelay)
    {
        $this->retriesDelay = $retriesDelay;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param  string      $title
     * @return $this|Queue
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return int
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * @param  int         $delay
     * @return $this|Queue
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * @return int
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * @param  int         $expiresIn
     * @return $this|Queue
     */
    public function setExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param  int         $timeout
     * @return $this|Queue
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param  Project     $project
     * @return $this|Queue
     */
    public function setProject(Project $project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Message[]|ArrayCollection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return Subscriber[]|ArrayCollection
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * @return int
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("project_id")
     */
    public function getProjectId()
    {
        return $this->getProject()->getId();
    }

    /**
     * @return int
     * @JMS\Groups({"size"})
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("size")
     */
    public function getSize()
    {
        return $this->getMessages()->count();
    }

}
