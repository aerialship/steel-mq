<?php

namespace AerialShip\SteelMqBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="smq_message")
 */
class Message
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $retriesRemaining;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime")
     */
    protected $availableAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $timeoutAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deletedAt;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=100)
     */
    protected $token;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $body;

    /**
     * @var Queue
     * @ORM\ManyToOne(
     *      targetEntity="Queue"
     * )
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $queue;

    /**
     *
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return $this|Message
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * @param \DateTime|null $completedAt
     * @return $this|Message
     */
    public function setCompletedAt($completedAt)
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return $this|Message
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

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
     * @return int
     */
    public function getRetriesRemaining()
    {
        return $this->retriesRemaining;
    }

    /**
     * @param int $retriesRemaining
     * @return $this|Message
     */
    public function setRetriesRemaining($retriesRemaining)
    {
        $this->retriesRemaining = $retriesRemaining;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getTakenAt()
    {
        return $this->takenAt;
    }

    /**
     * @param \DateTime|null $takenAt
     * @return $this|Message
     */
    public function setTakenAt($takenAt)
    {
        $this->takenAt = $takenAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getTimeoutAt()
    {
        return $this->timeoutAt;
    }

    /**
     * @param \DateTime|null $timeoutAt
     * @return $this|Message
     */
    public function setTimeoutAt($timeoutAt)
    {
        $this->timeoutAt = $timeoutAt;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param null|string $token
     * @return $this|Message
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return Queue
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @param Queue $queue
     * @return $this|Message
     */
    public function setQueue(Queue $queue)
    {
        $this->queue = $queue;

        return $this;
    }

}
