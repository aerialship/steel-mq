<?php

namespace AerialShip\SteelMqBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AerialShip\SteelMqBundle\Entity\Repository\MessageRepository")
 * @ORM\Table(name="smq_message", indexes={
 *      @ORM\Index(name="idx_available_at", columns={"queue_id", "available_at"})
 * })
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
     * @ORM\Column(type="integer")
     */
    protected $retriesRemaining;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $availableAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $timeoutAt;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=100, nullable=true, unique=true)
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
        $this->availableAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param  string        $body
     * @return $this|Message
     */
    public function setBody($body)
    {
        $this->body = $body;

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
     * @param  \DateTime     $createdAt
     * @return $this|Message
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getAvailableAt()
    {
        return $this->availableAt;
    }

    /**
     * @param  \DateTime|null $availableAt
     * @return $this|Message
     */
    public function setAvailableAt(\DateTime $availableAt)
    {
        $this->availableAt = $availableAt;

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
     * @param  int           $retriesRemaining
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
    public function getTimeoutAt()
    {
        return $this->timeoutAt;
    }

    /**
     * @param  \DateTime|null $timeoutAt
     * @return $this|Message
     */
    public function setTimeoutAt(\DateTime $timeoutAt = null)
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
     * @param  null|string   $token
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
     * @param  Queue         $queue
     * @return $this|Message
     */
    public function setQueue(Queue $queue)
    {
        $this->queue = $queue;

        return $this;
    }

}
