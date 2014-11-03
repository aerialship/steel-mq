<?php

namespace AerialShip\SteelMqBundle\Entity\Repository;

use AerialShip\SteelMqBundle\Entity\Message;
use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Model\Repository\MessageRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class MessageRepository extends EntityRepository implements MessageRepositoryInterface
{
    /**
     * @param Message $message
     * @param bool $flush
     * @return void
     */
    public function save(Message $message, $flush = true)
    {
        $this->_em->persist($message);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function clearQueue(Queue $queue)
    {
        $qb = $this->_em->createQueryBuilder();
        $q = $qb->update()
            ->set('deletedAt', new \DateTime())
            ->where('queue = :queue')
            ->setParameter('queue', $queue)
            ->getQuery();
        $q->execute();
    }
}
