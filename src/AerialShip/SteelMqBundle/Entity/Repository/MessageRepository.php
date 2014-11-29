<?php

namespace AerialShip\SteelMqBundle\Entity\Repository;

use AerialShip\SteelMqBundle\Entity\Message;
use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Helper\TokenHelper;
use AerialShip\SteelMqBundle\Model\Repository\MessageRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class MessageRepository extends EntityRepository implements MessageRepositoryInterface
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
     * @param  Message $message
     * @param  bool    $flush
     * @return void
     */
    public function save(Message $message, $flush = true)
    {
        $this->_em->persist($message);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param  Queue     $queue
     * @param  array     $options
     * @return Message[]
     */
    public function getMessages(Queue $queue, array $options)
    {
        $now = new \DateTime();
        $options['now'] = $now;
        $options['token'] = sprintf('%s-%s', $now->format('YmdHis'), TokenHelper::generate());
        $options['timeout_at'] = new \DateTime(sprintf('+%s seconds', $options['timeout']));

        $result = $this->transactional(function () use ($queue, $options) {
            // doctrine is throwing exception when order by is used in update query, so we're using native query
            $count = $this->_em->getConnection()->executeUpdate(
                sprintf(
                    'UPDATE smq_message
                    SET token=:token, timeout_at=:timeout
                    WHERE queue_id=:queue_id AND available_at<=:now AND token IS NULL
                    ORDER BY available_at ASC, id ASC
                    LIMIT %s',
                    $options['limit']
                ),
                array(
                    'token' => $options['token'],
                    'timeout' => $options['timeout_at']->format('Y-m-d H:i:s'),
                    'queue_id' => $queue->getId(),
                    'now' => $options['now']->format('Y-m-d H:i:s'),
                )
            );

            $qb = $this->_em->createQueryBuilder();
            $result = $qb->select('m')
                ->from('AerialShipSteelMqBundle:Message', 'm')
                ->where('m.token = :token')
                ->setParameter('token', $options['token'])
                ->setMaxResults($options['limit'])
                ->getQuery()
                ->getResult();

            if ($count != count($result)) {
                throw new \RuntimeException(sprintf('Expected %s messages but got only %s', $count, count($result)));
            }

            if ($options['delete']) {
                $qb->delete('AerialShipSteelMqBundle:Message', 'm')
                    ->where('m.token = :token')
                    ->setParameter('token', $options['token'])
                    ->getQuery()
                    ->execute();
            }

            return $result;
        });

        // transactional() replaces empty array with true
        return $result === true ? array() : $result;
    }

    /**
     * @param  Message $message
     * @return void
     */
    public function delete(Message $message)
    {
        $this->_em->remove($message);
        $this->_em->flush();
    }
}
