<?php

namespace AerialShip\SteelMqBundle\Entity\Repository;

use AerialShip\SteelMqBundle\Entity\Subscriber;
use AerialShip\SteelMqBundle\Model\Repository\SubscriberRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class SubscriberRepository extends EntityRepository implements SubscriberRepositoryInterface
{
    /**
     * @param $callback
     * @return mixed
     */
    public function transactional($callback)
    {
        return $this->_em->transactional($callback);
    }

    /**
     * @param Subscriber $subscriber
     * @param bool       $flush
     */
    public function save(Subscriber $subscriber, $flush = true)
    {
        $this->_em->persist($subscriber);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param Subscriber $subscriber
     */
    public function delete(Subscriber $subscriber)
    {
        $this->_em->remove($subscriber);
        $this->_em->flush();
    }

}
