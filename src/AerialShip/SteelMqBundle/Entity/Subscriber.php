<?php

namespace AerialShip\SteelMqBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="AerialShip\SteelMqBundle\Entity\Repository\SubscriberRepository")
 * @ORM\Table(name="smq_subscriber")
 */
class Subscriber
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
     * @ORM\Column(type="string", length=200)
     * @JMS\Expose
     */
    protected $url;

    /**
     * @var array
     * @ORM\Column(type="array")
     * @JMS\Expose
     */
    protected $headers = array();

    /**
     * @var Queue
     * @ORM\ManyToOne(
     *      targetEntity="Queue",
     *      inversedBy="subscribers"
     * )
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @JMS\Groups({"queue"})
     */
    protected $queue;

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param  array $headers
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

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
     * @return Queue
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @param  Queue            $queue
     * @return $this|Subscriber
     */
    public function setQueue(Queue $queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param  string           $url
     * @return $this|Subscriber
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}
