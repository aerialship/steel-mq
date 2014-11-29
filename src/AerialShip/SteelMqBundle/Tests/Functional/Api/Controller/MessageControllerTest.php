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
        $this->loadUserAndProjectData();

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
}
