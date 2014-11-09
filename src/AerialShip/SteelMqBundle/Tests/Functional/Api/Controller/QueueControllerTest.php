<?php

namespace AerialShip\SteelMqBundle\Tests\Functional\Api\Controller;

use AerialShip\SteelMqBundle\Entity\Message;
use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Tests\Functional\AbstractFunctionTestCase;

class QueueControllerTest extends AbstractFunctionTestCase
{
    /** @var int */
    private $projectId;

    /** @var  Project[] */
    private $allProjects;

    protected function setUp()
    {
        $this->loadQueueData();

        $projectRepo = $this->getProjectRepository();

        $project = $projectRepo->findOneBy([]);
        $this->projectId = $project->getId();

        $this->allProjects = $projectRepo->findBy([]);
    }

    public function testList()
    {
        $token = 'userToken';
        $client = static::createClient();
        $client->request('GET', sprintf('projects/%s/queues?token=%s', $this->allProjects[0]->getId(), $token));
        $response = $client->getResponse();
        $this->assertJsonResponse($response);

        $json = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertCount(1, $json);

        $this->assertArrayHasKey('project_id', $json[0]);
        $this->assertArrayNotHasKey('size', $json[0]);
        $this->assertArrayHasKey('id', $json[0]);
        $this->assertArrayHasKey('title', $json[0]);
        $this->assertArrayHasKey('push_type', $json[0]);
        $this->assertArrayHasKey('retries', $json[0]);
        $this->assertArrayHasKey('retries_delay', $json[0]);
        $this->assertArrayHasKey('error_queue', $json[0]);
        $this->assertArrayHasKey('timeout', $json[0]);
        $this->assertArrayHasKey('delay', $json[0]);
        $this->assertArrayHasKey('expires_in', $json[0]);

        $this->assertEquals($this->allProjects[0]->getId(), $json[0]['project_id']);
        $this->assertEquals('Main Queue P1', $json[0]['title']);
        $this->assertEquals(Queue::PUSH_TYPE_PULL, $json[0]['push_type']);
        $this->assertEquals(3, $json[0]['retries']);
        $this->assertEquals(600, $json[0]['retries_delay']);
        $this->assertNull($json[0]['error_queue']);
        $this->assertEquals(60, $json[0]['timeout']);
        $this->assertEquals(0, $json[0]['delay']);
        $this->assertEquals(604800, $json[0]['expires_in']);
    }

    public function testListEmptyProject()
    {
        $token = 'userToken';
        $client = static::createClient();
        $client->request('GET', sprintf('projects/%s/queues?token=%s', $this->allProjects[3]->getId(), $token));
        $response = $client->getResponse();
        $this->assertJsonResponse($response);

        $json = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertCount(0, $json);
    }

    public function testCreate()
    {
        $token = 'userToken';
        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('projects/%s/queues?token=%s', $this->allProjects[2]->getId(), $token),
            array(
                'queue' => array(
                    'title' => $expectedTitle = 'My New Queue',
                ),
            )
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response);

        $json = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));

        $this->assertArrayHasKey('project_id', $json);
        $this->assertArrayNotHasKey('size', $json);
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('title', $json);
        $this->assertArrayHasKey('push_type', $json);
        $this->assertArrayHasKey('retries', $json);
        $this->assertArrayHasKey('retries_delay', $json);
        $this->assertArrayHasKey('error_queue', $json);
        $this->assertArrayHasKey('timeout', $json);
        $this->assertArrayHasKey('delay', $json);
        $this->assertArrayHasKey('expires_in', $json);

        $this->assertEquals($this->allProjects[2]->getId(), $json['project_id']);
        $this->assertEquals($expectedTitle, $json['title']);
        $this->assertEquals(Queue::PUSH_TYPE_PULL, $json['push_type']);
        $this->assertEquals(3, $json['retries']);
        $this->assertEquals(600, $json['retries_delay']);
        $this->assertNull($json['error_queue']);
        $this->assertEquals(60, $json['timeout']);
        $this->assertEquals(0, $json['delay']);
        $this->assertEquals(604800, $json['expires_in']);
    }

    public function testUpdate()
    {
        /** @var Queue $queue */
        $queue = $this->getQueueRepository()->findOneBy(array('project' => $this->allProjects[0]));
        $this->assertNotNull($queue);
        $this->assertInstanceOf('AerialShip\SteelMqBundle\Entity\Queue', $queue);

        $token = 'userToken';
        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('projects/%s/queues/%s?token=%s', $this->allProjects[0]->getId(), $queue->getId(), $token),
            array(
                'queue' => array(
                    'title' => $expectedTitle = 'Main Queue P1 - modified',
                    'push_type' => $expectedPushType = Queue::PUSH_TYPE_UNICAST,
                    'retries' => $expectedRetries = 33,
                    'retries_delay' => $expectedRetriesDelay = 789,
                    'error_queue' => $expectedErrorQueue = 456,
                    'timeout' => $expectedTimeout = 78,
                    'delay' => $expectedDelay = 11,
                    'expires_in' => $expectedExpiresIn = 605123,
                ),
            )
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response);

        $json = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));

        $this->assertArrayHasKey('project_id', $json);
        $this->assertArrayNotHasKey('size', $json);
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('title', $json);
        $this->assertArrayHasKey('push_type', $json);
        $this->assertArrayHasKey('retries', $json);
        $this->assertArrayHasKey('retries_delay', $json);
        $this->assertArrayHasKey('error_queue', $json);
        $this->assertArrayHasKey('timeout', $json);
        $this->assertArrayHasKey('delay', $json);
        $this->assertArrayHasKey('expires_in', $json);

        $this->assertEquals($this->allProjects[0]->getId(), $json['project_id']);
        $this->assertEquals($expectedTitle, $json['title']);
        $this->assertEquals($expectedPushType, $json['push_type']);
        $this->assertEquals($expectedRetries, $json['retries']);
        $this->assertEquals($expectedRetriesDelay, $json['retries_delay']);
        $this->assertEquals($expectedErrorQueue, $json['error_queue']);
        $this->assertEquals($expectedTimeout, $json['timeout']);
        $this->assertEquals($expectedDelay, $json['delay']);
        $this->assertEquals($expectedExpiresIn, $json['expires_in']);
    }

    public function testInfo()
    {
        /** @var Queue $queue */
        $queue = $this->getQueueRepository()->findOneBy(array('project' => $this->allProjects[0]));
        $this->assertNotNull($queue);
        $this->assertInstanceOf('AerialShip\SteelMqBundle\Entity\Queue', $queue);

        $token = 'userToken';
        $client = static::createClient();
        $client->request(
            'GET',
            sprintf('projects/%s/queues/%s?token=%s', $this->allProjects[0]->getId(), $queue->getId(), $token)
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response);

        $json = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));

        $this->assertArrayHasKey('project_id', $json);
        $this->assertArrayHasKey('size', $json);
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('title', $json);
        $this->assertArrayHasKey('push_type', $json);
        $this->assertArrayHasKey('retries', $json);
        $this->assertArrayHasKey('retries_delay', $json);
        $this->assertArrayHasKey('error_queue', $json);
        $this->assertArrayHasKey('timeout', $json);
        $this->assertArrayHasKey('delay', $json);
        $this->assertArrayHasKey('expires_in', $json);

        $this->assertEquals($this->allProjects[0]->getId(), $json['project_id']);
        $this->assertEquals('Main Queue P1', $json['title']);
        $this->assertEquals(Queue::PUSH_TYPE_PULL, $json['push_type']);
        $this->assertEquals(3, $json['retries']);
        $this->assertEquals(600, $json['retries_delay']);
        $this->assertNull($json['error_queue']);
        $this->assertEquals(60, $json['timeout']);
        $this->assertEquals(0, $json['delay']);
        $this->assertEquals(604800, $json['expires_in']);
    }

    public function testDelete()
    {
        /** @var Queue $queue */
        $queue = $this->getQueueRepository()->findOneBy(array('project' => $this->allProjects[0]));
        $this->assertNotNull($queue);
        $this->assertInstanceOf('AerialShip\SteelMqBundle\Entity\Queue', $queue);

        $token = 'userToken';

        $client = static::createClient();
        $client->request('DELETE', sprintf('projects/%s/queues/%s?token=%s', $this->projectId, $queue->getId(), $token));

        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);
        $data = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($data));
        $this->assertTrue($data['success']);

        $queue = $this->getQueueRepository()->find($queue->getId());
        $this->assertNull($queue);
    }

    public function testDeleteNotYours()
    {
        /** @var Queue $queue */
        $queue = $this->getQueueRepository()->findOneBy(array('project' => $this->allProjects[0]));
        $this->assertNotNull($queue);
        $this->assertInstanceOf('AerialShip\SteelMqBundle\Entity\Queue', $queue);

        $client = static::createClient();
        $client->request('DELETE', sprintf('projects/%s/queues/%s?token=guestToken', $this->projectId, $queue->getId()));
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 403);

        $queue = $this->getQueueRepository()->find($queue->getId());
        $this->assertNotNull($queue);
    }

    public function testDeleteNonExisting()
    {
        $token = 'userToken';
        $queueId = null;
        for ($i = 0; $i<100; $i++) {
            $queueId = mt_rand(1000, 9999999);
            $queue = $this->getQueueRepository()->find($queueId);
            if (null === $queue) {
                break;
            }
            $queueId = null;
        }
        if (null === $queueId) {
            throw new \RuntimeException('Unable to find id of non-existing queue');
        }

        $client = static::createClient();
        $client->request('DELETE', sprintf('projects/%s/queues/%s?token=%s', $this->projectId, $queueId, $token));
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 404);

        $json = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));

        $this->assertArrayHasKey('code', $json);
        $this->assertArrayHasKey('message', $json);

        $this->assertEquals(404, $json['code']);
        // TODO assert appropriate message - current one exposes class namespace
    }

    public function testClear()
    {
        $token = 'userToken';

        /** @var Queue $queue */
        $queue = $this->getQueueRepository()->findOneBy(array('project' => $this->allProjects[0]));
        $this->assertNotNull($queue);
        $this->assertInstanceOf('AerialShip\SteelMqBundle\Entity\Queue', $queue);

        $this->getMessageRepository()->save((new Message())
            ->setQueue($queue)
            ->setAvailableAt(new \DateTime())
            ->setBody('body')
            ->setRetriesRemaining(5)
        );

        $queue = $this->getQueueRepository()->findOneBy(array('project' => $this->allProjects[0]));
        $this->assertEquals(1, $queue->getSize());

        $client = static::createClient();
        $client->request('POST', sprintf('projects/%s/queues/%s/clear?token=%s', $this->projectId, $queue->getId(), $token));
        $response = $client->getResponse();
        $this->assertJsonResponse($response);

        $queue = $this->getQueueRepository()->findOneBy(array('project' => $this->allProjects[0]));
        $this->assertEquals(0, $queue->getSize());
    }
}
