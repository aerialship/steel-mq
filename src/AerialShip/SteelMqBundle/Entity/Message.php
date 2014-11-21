<?php

namespace AerialShip\SteelMqBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="AerialShip\SteelMqBundle\Entity\Repository\MessageRepository")
 * @ORM\Table(name="smq_message", indexes={
 *      @ORM\Index(name="idx_available_at", columns={"queue_id", "available_at"})
 * })
 * @JMS\ExclusionPolicy("all")
 */
class Message
{
    const STATUS_NOT_AVAILABLE = 'not_available';
    const STATUS_AVAILABLE = 'available';
    const STATUS_TAKEN = 'taken';

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $retriesRemaining;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @JMS\Expose
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @JMS\Expose
     */
    protected $availableAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Expose
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
     * @JMS\Expose
     */
    protected $body;

    /**
     * @var Queue
     * @ORM\ManyToOne(
     *      targetEntity="Queue",
     *      inversedBy="messages"
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

    /**
     * @return int|null
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("queue_id")
     */
    public function getQueueId()
    {
        return $this->getQueue() === null ? null : $this->getQueue()->getId();
    }

    /**
     * @return string
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("status")
     */
    public function getStatus()
    {
        if ($this->token) {
            return static::STATUS_TAKEN;
        }
        if ($this->availableAt->getTimestamp() <= time()) {
            return static::STATUS_AVAILABLE;
        }

        return static::STATUS_NOT_AVAILABLE;
    }

    // --------------------------------------------------------------------------------

    /**
     * @param  int           $value
     * @return $this|Message
     */
    public function setRetries($value)
    {
        $this->setRetriesRemaining($value);

        return $this;
    }

    /**
     * @return int
     */
    public function getRetries()
    {
        return $this->getRetriesRemaining();
    }

    /**
     * @param  int           $value
     * @return $this|Message
     */
    public function setDelay($value)
    {
        $this->setAvailableAt(new \DateTime(sprintf('+%s seconds', intval($value))));

        return $this;
    }

    public function getDelay()
    {
        if (null === $this->getAvailableAt()) {
            return 0;
        }

        return abs($this->getAvailableAt()->getTimestamp() - time());
    }
}
