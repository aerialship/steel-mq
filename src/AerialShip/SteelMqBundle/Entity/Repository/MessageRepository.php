<?php

namespace AerialShip\SteelMqBundle\Entity\Repository;

use AerialShip\SteelMqBundle\Entity\Message;
use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Error\SafeException;
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

    /**
     * @param  Message $message
     * @param  int     $delay
     * @return void
     */
    public function release(Message $message, $delay)
    {
        if ($message->getStatus() != Message::STATUS_TAKEN) {
            throw new SafeException('Only taken messages can be released');
        }

        $availableAt = new \DateTime(sprintf('+%s SECOND', intval($delay)));
        $message
            ->setToken(null)
            ->setAvailableAt($availableAt)
            ->setTimeoutAt(null)
        ;
        $this->_em->persist($message);
        $this->_em->flush();
    }

    /**
     * @param \DateTime $now
     *
     * @return int Number of deleted messages
     */
    public function deleteExpired(\DateTime $now = null)
    {
        if (null === $now) {
            $now = new \DateTime();
        }

        $sql = "DELETE m
            FROM smq_message m
            JOIN smq_queue q ON q.id=m.queue_id
            WHERE m.token IS NULL
            AND (m.available_at + INTERVAL q.expires_in SECOND) < :now ";

        $result = $this->_em->getConnection()->executeUpdate($sql, array(
            'now' => $now->format('Y-m-d H:i:s'),
        ));

        return $result;
    }

    /**
     * @param \DateTime $now
     *
     * @return array [totalCount, nonDeletedCount, movedCount]
     */
    public function manageTimeout(\DateTime $now = null)
    {
        if (null === $now) {
            $now = new \DateTime();
        }

        return $this->_em->transactional(function () use ($now) {
            return $this->timeoutTransactional($now);
        });
    }

    /**
     * @param \DateTime $now
     *
     * @return array [totalCount, nonDeletedCount, movedCount]
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function timeoutTransactional(\DateTime $now)
    {
        $totalCount = $this->_em->getConnection()->executeUpdate(
            'UPDATE smq_message
            SET locked=1
            WHERE token IS NOT NULL
            AND timeout_at < :now ',
            array(
                'now' => $now->format('Y-m-d H:i:s'),
            )
        );

        // copy all retries_remaining=0 to existing error queue
        $movedCount = $this->_em->getConnection()->executeUpdate(
            'INSERT INTO smq_message (queue_id, locked, retries_remaining, created_at, available_at, timeout_at, token, body)
            SELECT e.id, 0, e.retries, :now, :now + INTERVAL e.delay SECOND, NULL, NULL, m.body
            FROM smq_message m
            JOIN smq_queue q ON m.queue_id=q.id
            JOIN smq_queue e ON e.id=q.error_queue
            WHERE m.token IS NOT NULL
            AND m.timeout_at < :now
            AND m.retries_remaining = 0
            AND m.locked = 1',
            array(
                'now' => $now->format('Y-m-d H:i:s'),
            )
        );

        // delete all retries_remaining=0
        $this->_em->getConnection()->executeUpdate(
            'DELETE m
            FROM smq_message m
            JOIN smq_queue q ON m.queue_id=q.id
            WHERE m.token IS NOT NULL
            AND m.timeout_at < :now
            AND m.retries_remaining = 0
            AND m.locked = 1',
            array(
                'now' => $now->format('Y-m-d H:i:s'),
            )
        );

        // decrement retries_remaining, clear lock, and put back to available
        $nonDeletedCount = $this->_em->getConnection()->executeUpdate(
            'UPDATE smq_message m
            JOIN smq_queue q ON m.queue_id=q.id
            SET m.retries_remaining = m.retries_remaining - 1,
            m.available_at = :now + INTERVAL q.delay SECOND,
            m.locked = 0,
            m.token = NULL,
            m.timeout_at = NULL
            WHERE m.token IS NOT NULL
            AND m.timeout_at < :now
            AND m.retries_remaining > 0
            AND m.locked = 1',
            array(
                'now' => $now->format('Y-m-d H:i:s'),
            )
        );

        return array($totalCount, $nonDeletedCount, $movedCount);
    }
}
