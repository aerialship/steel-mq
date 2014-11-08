<?php

namespace AerialShip\SteelMqBundle\Tests\Functional\Api\Controller;

use AerialShip\SteelMqBundle\Entity\Message;
use AerialShip\SteelMqBundle\Tests\Functional\AbstractFunctionTestCase;

class QueueControllerTest extends AbstractFunctionTestCase
{
    /** @var int */
    private $projectId;

    protected function setUp()
    {
        $projectRepo = $this->getProjectRepository();

        $project = $projectRepo->findOneBy([]);
        $this->projectId = $project->getId();
    }

    public function testDelete()
    {
        $token = 'userToken';
        $queueId = $this->createQueue($token);

        $client = static::createClient();
        $client->request('DELETE', sprintf('projects/%s/queues/%s?token=%s', $this->projectId, $queueId, $token));

        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);
        $data = json_decode($response->getContent());
        $this->assertTrue($data->success);
    }

    public function testDeleteNotYours()
    {
        $queueId = $this->createQueue('userToken');

        $client = static::createClient();
        $client->request('DELETE', sprintf('projects/%s/queues/%s?token=guestToken', $this->projectId, $queueId));
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 403);

    }

    public function testDeleteNonExisting()
    {
        $token = 'userToken';
        $queueId = $this->createQueue($token);

        $client = static::createClient();
        $client->request('DELETE', sprintf('projects/%s/queues/%s?token=%s', $this->projectId, $queueId, $token));

        $client = static::createClient();
        $client->request('DELETE', sprintf('projects/%s/queues/%s?token=%s', $this->projectId, $queueId, $token));
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testClear()
    {
        $token = 'userToken';
        $queueId = $this->createQueue($token);

        $queue = $this->getQueueRepository()->find($queueId);

        $this->getMessageRepository()->save((new Message())
            ->setQueue($queue)
            ->setAvailableAt(new \DateTime())
            ->setBody('body')
            ->setRetriesRemaining(5)
        );

        $client = static::createClient();
        $client->request('GET', sprintf('projects/%s/queues/%s?token=%s', $this->projectId, $queueId, $token));
        $response = $client->getResponse();
        $this->assertJsonResponse($response);
        $json = json_decode($response->getContent(), true);
        $this->assertEquals(1, $json['size']);

        $client = static::createClient();
        $client->request('POST', sprintf('projects/%s/queues/%s/clear?token=%s', $this->projectId, $queueId, $token));
        $response = $client->getResponse();
        $this->assertJsonResponse($response);

        $client = static::createClient();
        $client->request('GET', sprintf('projects/%s/queues/%s?token=%s', $this->projectId, $queueId, $token));
        $response = $client->getResponse();
        $this->assertJsonResponse($response);
        $json = json_decode($response->getContent(), true);
        $this->assertEquals(0, $json['size']);
    }

    /**
     * @param  string $token
     * @return int
     */
    private function createQueue($token)
    {
        // Create Queue
        $parameters = [
            "queue" => [
                "title" => "Queue 111",
            ]
        ];
        $client = static::createClient();
        $client->request('POST', sprintf('projects/%s/queues?token=%s', $this->projectId, $token), $parameters);
        $response = $client->getResponse();

        $content = json_decode($response->getContent());

        return $content->id;
    }
}
