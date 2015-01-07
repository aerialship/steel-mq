<?php

namespace AerialShip\SteelMqBundle\Tests\Functional\Entity\Repository;

use AerialShip\SteelMqBundle\Entity\Message;
use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Helper\TokenHelper;
use AerialShip\SteelMqBundle\Tests\Functional\AbstractFunctionTestCase;

class MessageRepositoryTest extends AbstractFunctionTestCase
{
    /** @var Project */
    private $project;

    /** @var Project[] */
    private $allProjects;

    protected function setUp()
    {
        $this->loadQueueData();

        $projectRepo = $this->getProjectRepository();
        $this->project = $projectRepo->findOneBy([]);
        $this->allProjects = $projectRepo->findBy([]);
    }

    public function testDeleteExpiredDontDeleteNonExpired()
    {
        $queue = $this->getQueueRepository()->findOneBy(array('project' => $this->project));

        $messageId = $this->createMessage($queue)->getId();

        $countDeleted = $this->getMessageRepository()->deleteExpired();

        $this->assertEquals(0, $countDeleted);
        $message = $this->getMessageRepository()->find($messageId);
        $this->assertNotNull($message);
    }

    public function testDeleteExpiredDeletesExpired()
    {
        $queue = $this->getQueueRepository()->findOneBy(array('project' => $this->project));

        $this->createMessage($queue, 3, new \DateTime('-1 year'));

        $countDeleted = $this->getMessageRepository()->deleteExpired();

        $this->assertEquals(1, $countDeleted);
        $arr = $this->getMessageRepository()->findBy(array('queue' => $queue));
        $this->assertEmpty($arr);
    }

    public function testManageTimeoutDoesNothingToNonTaken()
    {
        $queue = $this->getQueueRepository()->findOneBy(array('project' => $this->project));

        $this->createMessage($queue);

        list($totalCount, $nonDeletedCount, $movedCount) = $this->getMessageRepository()->manageTimeout();

        $this->assertEquals(0, $totalCount);
        $this->assertEquals(0, $nonDeletedCount);
        $this->assertEquals(0, $movedCount);

        $arr = $this->getMessageRepository()->findBy(array('queue' => $queue));
        $this->assertCount(1, $arr);
    }

    public function testManageTimeoutDoesNothingToNonExpired()
    {
        $queue = $this->getQueueRepository()->findOneBy(array('project' => $this->project));

        $this->createMessage($queue, 3, null, new \DateTime('+1 minute'));

        list($totalCount, $nonDeletedCount, $movedCount) = $this->getMessageRepository()->manageTimeout();

        $this->assertEquals(0, $totalCount);
        $this->assertEquals(0, $nonDeletedCount);
        $this->assertEquals(0, $movedCount);

        $arr = $this->getMessageRepository()->findBy(array('queue' => $queue));
        $this->assertCount(1, $arr);
    }

    public function testManageTimeoutDecreaseRetriesRemaining()
    {
        $queue = $this->getQueueRepository()->findOneBy(array('project' => $this->project));

        $messageId = $this->createMessage($queue, 3, null, new \DateTime('-1 second'))->getId();

        list($totalCount, $nonDeletedCount, $movedCount) = $this->getMessageRepository()->manageTimeout();

        $this->assertEquals(1, $totalCount);
        $this->assertEquals(1, $nonDeletedCount);
        $this->assertEquals(0, $movedCount);

        $arr = $this->getMessageRepository()->findBy(array('queue' => $queue));
        $this->assertCount(1, $arr);

        $this->getEm()->clear();

        $message = $this->getMessageRepository()->find($messageId);
        $this->assertNotNull($message);
        $this->assertEquals(2, $message->getRetriesRemaining());
    }

    public function testManageTimeoutDeletesWhenRetriesRemainingZeroAndNoErrorQueue()
    {
        $queue = $this->getQueueRepository()->findOneBy(array('project' => $this->project));

        $messageId = $this->createMessage($queue, 0, null, new \DateTime('-1 second'))->getId();

        list($totalCount, $nonDeletedCount, $movedCount) = $this->getMessageRepository()->manageTimeout();

        $this->assertEquals(1, $totalCount);
        $this->assertEquals(0, $nonDeletedCount);
        $this->assertEquals(0, $movedCount);

        $arr = $this->getMessageRepository()->findBy(array('queue' => $queue));
        $this->assertCount(0, $arr);

        $this->getEm()->clear();

        $message = $this->getMessageRepository()->find($messageId);
        $this->assertNull($message);
    }

    public function testManageTimeoutMovesToErrorQueue()
    {
        $mainQueue = $errorQueue = null;
        foreach ($this->project->getQueues() as $queue) {
            if ($mainQueue == null) {
                $mainQueue = $queue;
            } elseif ($errorQueue == null) {
                $errorQueue = $queue;
            } else {
                break;
            }
        }

        $mainQueue->setErrorQueue($errorQueue->getId());
        $this->getQueueRepository()->save($mainQueue);

        $messageId = $this->createMessage(
            $mainQueue,
            0,
            null,
            new \DateTime('-1 second'),
            null,
            $expectedBody = 'message to be moved to error queue'
        )->getId();

        list($totalCount, $nonDeletedCount, $movedCount) = $this->getMessageRepository()->manageTimeout();

        $this->assertEquals(1, $totalCount);
        $this->assertEquals(0, $nonDeletedCount);
        $this->assertEquals(1, $movedCount);

        $arr = $this->getMessageRepository()->findBy(array('queue' => $mainQueue));
        $this->assertCount(0, $arr);

        $arr = $this->getMessageRepository()->findBy(array('queue' => $errorQueue));
        $this->assertCount(1, $arr);

        $this->getEm()->clear();

        $message = $this->getMessageRepository()->find($messageId);
        $this->assertNull($message);

        $message = $this->getMessageRepository()->findOneBy(array('queue' => $errorQueue));
        $this->assertNotNull($message);
        $this->assertEquals($expectedBody, $message->getBody());
    }

    private function createMessage(
        Queue $queue,
        $retriesRemaining = 3,
        \DateTime $availableAt = null,
        \DateTime $timeoutAt = null,
        \DateTime $createdAt = null,
        $body = 'message body'
    ) {
        $message = new Message();
        $message
            ->setQueue($queue)
            ->setAvailableAt($availableAt ?: new \DateTime())
            ->setCreatedAt($createdAt ?: new \DateTime('2014-01-01 12:00:00'))
            ->setTimeoutAt($timeoutAt)
            ->setToken($timeoutAt ? TokenHelper::generate() : null)
            ->setRetriesRemaining($retriesRemaining)
            ->setBody($body)
        ;

        $this->getMessageRepository()->save($message);

        return $message;
    }
}
