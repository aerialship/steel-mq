<?php
namespace AerialShip\SteelMqBundle\Tests\Functional\Controller\Api;

use AerialShip\SteelMqBundle\Tests\Functional\AbstractFunctionTestCase;

class QueueControllerTest extends AbstractFunctionTestCase
{

    /** @var int */
    private $projectId;

    protected function setUp()
    {
        $projectRepo = $this->getService('doctrine')
            ->getManager()
            ->getRepository('AerialShipSteelMqBundle:Project');

        $project = $projectRepo->findOneBy([]);
        $this->projectId = $project->getId();
    }

    public function testDelete()
    {
        $token = 'token=userToken';
        $queueId = $this->createQueue($token);

        $client = static::createClient();
        $client->request('DELETE', sprintf('projects/%s/queues/%s?%s', $this->projectId, $queueId, $token));

        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);
        $data = json_decode($response->getContent());
        $this->assertTrue($data->success);

    }

    public function testDeleteNotYours()
    {
        $queueId = $this->createQueue('token=userToken');

        $client = static::createClient();
        $client->request('DELETE', sprintf('projects/%s/queues/%s?%s', $this->projectId, $queueId, 'token=guestToken'));
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 403);

    }

    public function testDeleteNonExisting()
    {
        $token = 'token=userToken';
        $queueId = $this->createQueue($token);

        $client = static::createClient();
        $client->request('DELETE', sprintf('projects/%s/queues/%s?%s', $this->projectId, $queueId, $token));

        $client = static::createClient();
        $client->request('DELETE', sprintf('projects/%s/queues/%s?%s', $this->projectId, $queueId, $token));
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 404);
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
                "delay" => 333
            ]
        ];
        $client = static::createClient();
        $client->request('POST', sprintf('projects/%s/queues?%s', $this->projectId, $token), $parameters);
        $response = $client->getResponse();

        $content = json_decode($response->getContent());

        return $content->id;
    }
}
