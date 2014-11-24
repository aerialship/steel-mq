<?php

namespace AerialShip\SteelMqBundle\Tests\Functional\Api\Controller;

use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Entity\Subscriber;
use AerialShip\SteelMqBundle\Tests\Functional\AbstractFunctionTestCase;

class SubscriberControllerTest extends AbstractFunctionTestCase
{

    /** @var Subscriber $subscriber */
    private $subscriber;

    /** @var Queue $emptyQueue */
    private $emptyQueue;

    protected function setUp()
    {
        $this->loadUserAndProjectData();

        $subscriberRepo = $this->getSubscriberRepository();
        $queueRepo = $this->getQueueRepository();

        $this->subscriber = $subscriberRepo->findOneBy([]);
        $queues = $queueRepo->findBy([]);
        foreach ($queues as $queue) {
            if ($queue->getSubscribers()->isEmpty()) {
                $this->emptyQueue = $queue;
                break;
            }
        }
    }

    public function testList()
    {
        $token = 'userToken';
        $client = static::createClient();
        $client->request(
            'GET',
            sprintf(
                'projects/%s/queues/%s/subscribers?token=%s',
                $this->subscriber->getQueue()->getProject()->getId(),
                $this->subscriber->getQueue()->getId(),
                $token
            )
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response);

        $json = json_decode($response->getContent(), true);

        $this->assertTrue(is_array($json));
        $this->assertCount(1, $json);

        $subscriberData = $json[0];

        $this->assertArrayHasKey('id', $subscriberData);
        $this->assertArrayHasKey('url', $subscriberData);
        $this->assertArrayHasKey('headers', $subscriberData);

        $this->assertInternalType('array', $subscriberData['headers']);
        $this->assertCount(2, $subscriberData['headers']);

        $this->assertEquals($this->subscriber->getId(), $subscriberData['id']);
        $this->assertEquals('http://some.subscriber.com/steal_mq_hook', $subscriberData['url']);
        $this->assertEquals('Content-Type', array_keys($subscriberData['headers'])[0]);
        $this->assertEquals('X-Custom', array_keys($subscriberData['headers'])[1]);

    }

    public function testEmptyList()
    {
        $token = 'userToken';
        $client = static::createClient();
        $client->request(
            'GET',
            sprintf(
                'projects/%s/queues/%s/subscribers?token=%s',
                $this->emptyQueue->getProject()->getId(),
                $this->emptyQueue->getId(),
                $token
            )
        );
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
            sprintf(
                'projects/%s/queues/%s/subscribers?token=%s',
                $this->emptyQueue->getProject()->getId(),
                $this->emptyQueue->getId(),
                $token
            ),
            [
                "subscriber" => [
                    "url" => "http://some.subscriber.com/steel_mq_hook",
                    "headers" => [
                        "Content-Type" => [
                            "application/json",
                        ],
                    ],
                ],
            ]
        );

        $response = $client->getResponse();
        $this->assertJsonResponse($response);

        $json = json_decode($response->getContent(), true);

        $this->assertTrue(is_array($json));
        $this->assertArrayHasKey("id", $json);
        $this->assertArrayHasKey("success", $json);

        $this->assertTrue($json['success']);
    }

    public function testDelete()
    {
        $token = 'userToken';
        $client = static::createClient();
        $client->request(
            'DELETE',
            sprintf(
                'projects/%s/queues/%s/subscribers/%s?token=%s',
                $this->subscriber->getQueue()->getProject()->getId(),
                $this->subscriber->getQueue()->getId(),
                $this->subscriber->getId(),
                $token
            )
        );

        $response = $client->getResponse();
        $this->assertJsonResponse($response);

        $json = json_decode($response->getContent(), true);

        $this->assertTrue(is_array($json));
        $this->assertArrayHasKey("success", $json);

        $this->assertTrue($json['success']);
    }
}
