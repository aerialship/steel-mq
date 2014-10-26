<?php

namespace AerialShip\SteelMqBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="smq_queue")
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
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="integer", length=100)
     */
    protected $title;

    /**
     * @var string
     */
    protected $pushType = self::PUSH_TYPE_PULL;

    /**
     * @var int
     */
    protected $retries;

    /**
     * @var int
     */
    protected $retriesDelay;

    /**
     * @var string|null
     */
    protected $errorQueue;

    /**
     * @var Project
     * @ORM\ManyToOne(
     *      targetEntity="Project",
     *      inversedBy="queues"
     * )
     */
    protected $project;

    /**
     * @var Subscriber[]|ArrayCollection
     * @ORM\ManyToOne(
     *      targetEntity="Subscriber",
     *      inversedBy="queue"
     * )
     */
    protected $subscribers;

    /**
     *
     */
    public function __construct()
    {
        $this->subscribers = new ArrayCollection();
    }

    /**
     * @return null|string
     */
    public function getErrorQueue()
    {
        return $this->errorQueue;
    }

    /**
     * @param null|string $errorQueue
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
     * @param string $pushType
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
     * @param int $retries
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
     * @param int $retriesDelay
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
     * @param string $title
     * @return $this|Queue
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

}
