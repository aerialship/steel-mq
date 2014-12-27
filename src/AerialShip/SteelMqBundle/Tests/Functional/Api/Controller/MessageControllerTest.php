<?php

namespace AerialShip\SteelMqBundle\Tests\Functional\Api\Controller;

use AerialShip\SteelMqBundle\Entity\Message;
use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Tests\Functional\AbstractFunctionTestCase;

class MessageControllerTest extends AbstractFunctionTestCase
{
    /** @var int */
    private $projectId;

    /** @var  Project[] */
    private $allProjects;

    protected function setUp()
    {
        $this->loadMessageData();

        $projectRepo = $this->getProjectRepository();

        $project = $projectRepo->findOneBy([]);
        $this->projectId = $project->getId();

        $this->allProjects = $projectRepo->findBy([]);
    }

    public function testAdd()
    {
        $token = 'userToken';

        /** @var Queue $queue */
        $queue = $this->getQueueRepository()->findOneBy(array('project' => $this->allProjects[0]));
        $this->assertNotNull($queue);
        $this->assertInstanceOf('AerialShip\SteelMqBundle\Entity\Queue', $queue);

        $client = static::createClient();

        $client->request(
            'POST',
            sprintf('projects/%s/queues/%s/messages?token=%s', $this->allProjects[0]->getId(), $queue->getId(), $token),
            array(
                'messages' => array(
                    0 => array(
                        'body' => $expectedBody1 = 'message 1 with defaults',
                    ),
                    1 => array(
                        'body' => $expectedBody2 = 'message 2 overridden',
                        'retries' => $expectedRetries2 = 22,
                        'delay' => $expectedDelay2 = 2222,
                    ),
                ),
            )
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response);

        $json = json_decode($response->getContent(), true);

        $this->assertTrue(is_array($json));
        $this->assertCount(2, $json);

        for ($i = 0; $i<2; $i++) {
            $this->assertArrayHasKey('queue_id', $json[$i]);
            $this->assertArrayHasKey('status', $json[$i]);
            $this->assertArrayHasKey('id', $json[$i]);
            $this->assertArrayHasKey('retries_remaining', $json[$i]);
            $this->assertArrayHasKey('created_at', $json[$i]);
            $this->assertArrayHasKey('available_at', $json[$i]);
            $this->assertArrayHasKey('timeout_at', $json[$i]);
            $this->assertArrayHasKey('body', $json[$i]);
        }

        $this->assertEquals($queue->getId(), $json[0]['queue_id']);
        $this->assertEquals(Message::STATUS_AVAILABLE, $json[0]['status']);
        $this->assertEquals($queue->getRetries(), $json[0]['retries_remaining']);
        $this->assertLessThan(4, abs(strtotime($json[0]['created_at']) - time()));
        $this->assertLessThan(4, abs(strtotime($json[0]['available_at']) - time()));
        $this->assertNull($json[0]['timeout_at']);
        $this->assertEquals($expectedBody1, $json[0]['body']);

        $this->assertEquals($queue->getId(), $json[1]['queue_id']);
        $this->assertEquals(Message::STATUS_NOT_AVAILABLE, $json[1]['status']);
        $this->assertEquals($expectedRetries2, $json[1]['retries_remaining']);
        $this->assertLessThan(4, abs(strtotime($json[1]['created_at']) - time()));
        $this->assertLessThan(4, abs(strtotime($json[1]['available_at']) - time()) - $expectedDelay2);
        $this->assertNull($json[1]['timeout_at']);
        $this->assertEquals($expectedBody2, $json[1]['body']);
    }

    public function testWebhook()
    {
        $token = 'userToken';

        /** @var Queue $queue */
        $queue = $this->getQueueRepository()->findOneBy(array('project' => $this->allProjects[0]));
        $this->assertNotNull($queue);
        $this->assertInstanceOf('AerialShip\SteelMqBundle\Entity\Queue', $queue);

        $client = static::createClient();

        $client->request(
            'POST',
            sprintf('projects/%s/queues/%s/messages/webhook?token=%s', $this->allProjects[0]->getId(), $queue->getId(), $token),
            array(),
            array(),
            array(),
            $expectedBody = 'message body'
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response);

        $json = json_decode($response->getContent(), true);

        $this->assertTrue(is_array($json));
        $this->assertCount(1, $json);

        $this->assertArrayHasKey('queue_id', $json[0]);
        $this->assertArrayHasKey('status', $json[0]);
        $this->assertArrayHasKey('id', $json[0]);
        $this->assertArrayHasKey('retries_remaining', $json[0]);
        $this->assertArrayHasKey('created_at', $json[0]);
        $this->assertArrayHasKey('available_at', $json[0]);
        $this->assertArrayHasKey('timeout_at', $json[0]);
        $this->assertArrayHasKey('body', $json[0]);

        $this->assertEquals($queue->getId(), $json[0]['queue_id']);
        $this->assertEquals(Message::STATUS_AVAILABLE, $json[0]['status']);
        $this->assertEquals($queue->getRetries(), $json[0]['retries_remaining']);
        $this->assertLessThan(4, abs(strtotime($json[0]['created_at']) - time()));
        $this->assertLessThan(4, abs(strtotime($json[0]['available_at']) - time()));
        $this->assertNull($json[0]['timeout_at']);
        $this->assertEquals($expectedBody, $json[0]['body']);
    }

    public function testGetOneMessage()
    {
        $token = 'userToken';

        /** @var Queue $queue */
        $queue = $this->allProjects[0]->getQueues()->first();
        $this->assertNotNull($queue);
        $this->assertInstanceOf('AerialShip\SteelMqBundle\Entity\Queue', $queue);

        $client = static::createClient();

        $client->request(
            'GET',
            sprintf('projects/%s/queues/%s/messages?token=%s', $this->allProjects[0]->getId(), $queue->getId(), $token)
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response);

        $json = json_decode($response->getContent(), true);

        $this->assertTrue(is_array($json));
        $this->assertCount(1, $json);
        $this->assertArrayHasKey('queue_id', $json[0]);
        $this->assertArrayHasKey('status', $json[0]);
        $this->assertArrayHasKey('id', $json[0]);
        $this->assertArrayHasKey('retries_remaining', $json[0]);
        $this->assertArrayHasKey('created_at', $json[0]);
        $this->assertArrayHasKey('available_at', $json[0]);
        $this->assertArrayHasKey('timeout_at', $json[0]);
        $this->assertArrayHasKey('body', $json[0]);

        $this->assertEquals($queue->getId(), $json[0]['queue_id']);

        $message = $this->loadMessage($json[0]['id']);
        $this->assertEquals(Message::STATUS_TAKEN, $message->getStatus());
        $this->assertNotNull($message->getToken());
        $this->assertGreaterThanOrEqual(new \DateTime(), $message->getTimeoutAt());
    }

    public function testGetMessageById()
    {
        $token = 'userToken';

        /** @var Queue $queue */
        $queue = $this->allProjects[0]->getQueues()->first();
        $this->assertNotNull($queue);
        $this->assertInstanceOf('AerialShip\SteelMqBundle\Entity\Queue', $queue);

        /** @var Message $message */
        $message = $queue->getMessages()->first();
        $this->assertNotNull($message);
        $this->assertInstanceOf('AerialShip\SteelMqBundle\Entity\Message', $message);

        $client = static::createClient();

        $client->request(
            'GET',
            sprintf('projects/%s/queues/%s/messages/%s?token=%s', $this->allProjects[0]->getId(), $queue->getId(), $message->getId(), $token)
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response);

        $json = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('queue_id', $json);
        $this->assertArrayHasKey('status', $json);
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('retries_remaining', $json);
        $this->assertArrayHasKey('created_at', $json);
        $this->assertArrayHasKey('available_at', $json);
        $this->assertArrayHasKey('body', $json);

        $this->assertEquals($queue->getId(), $json['queue_id']);
        $this->assertEquals($message->getStatus(), $json['status']);
        $this->assertEquals($message->getId(), $json['id']);
        $this->assertEquals($message->getRetriesRemaining(), $json['retries_remaining']);
        $this->assertEquals($message->getCreatedAt()->getTimestamp(), strtotime($json['created_at']));
        $this->assertEquals($message->getAvailableAt()->getTimestamp(), strtotime($json['available_at']));
        $this->assertEquals($message->getBody(), $json['body']);
    }

    public function testGetMessageByIdNonExisting()
    {
        $token = 'userToken';

        $client = static::createClient();

        $client->request('GET', sprintf('projects/999999/queues/999999/messages/9999?token=%s', $token));
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testDelete()
    {
        $token = 'userToken';

        /** @var Queue $queue */
        $queue = $this->allProjects[0]->getQueues()->first();
        $this->assertNotNull($queue);
        $this->assertInstanceOf('AerialShip\SteelMqBundle\Entity\Queue', $queue);

        /** @var Message $message */
        $message = $queue->getMessages()->first();
        $this->assertNotNull($message);
        $this->assertInstanceOf('AerialShip\SteelMqBundle\Entity\Message', $message);

        $oldDeleteCount = $queue->getDeletedCount();

        $client = static::createClient();

        $client->request(
            'DELETE',
            sprintf('projects/%s/queues/%s/messages/%s?token=%s', $this->allProjects[0]->getId(), $queue->getId(), $message->getId(), $token)
        );

        $response = $client->getResponse();
        $this->assertJsonResponse($response);

        $json = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));

        $this->assertArrayHasKey('success', $json);
        $this->assertTrue($json['success']);

        $message = $this->getMessageRepository()->find($message->getId());
        $this->assertNull($message);

        $queue = $this->getQueueRepository()->find($queue->getId());
        $this->assertEquals($oldDeleteCount + 1, $queue->getDeletedCount());
    }

    public function testRelease()
    {
        $token = 'userToken';

        /** @var Queue $queue */
        $queue = $this->allProjects[0]->getQueues()->first();
        $this->assertNotNull($queue);
        $this->assertInstanceOf('AerialShip\SteelMqBundle\Entity\Queue', $queue);

        // get a message
        $client = static::createClient();
        $client->request(
            'GET',
            sprintf('projects/%s/queues/%s/messages?token=%s', $this->allProjects[0]->getId(), $queue->getId(), $token)
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response);

        $json = json_decode($response->getContent(), true);

        $this->assertTrue(is_array($json));
        $this->assertCount(1, $json);

        $messageId = $json[0]['id'];

        // load message
        $message = $this->loadMessage($messageId);
        $this->assertEquals(Message::STATUS_TAKEN, $message->getStatus());

        // release taken message
        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('projects/%s/queues/%s/messages/%s/release?token=%s', $this->allProjects[0]->getId(), $queue->getId(), $message->getId(), $token)
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response);
        $json = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertArrayHasKey('success', $json);

        // load message
        $message = $this->loadMessage($messageId);
        $this->assertEquals(Message::STATUS_AVAILABLE, $message->getStatus());
        $this->assertLessThanOrEqual(new \DateTime(), $message->getAvailableAt());
        $this->assertNull($message->getToken());
        $this->assertNull($message->getTimeoutAt());
    }
}
