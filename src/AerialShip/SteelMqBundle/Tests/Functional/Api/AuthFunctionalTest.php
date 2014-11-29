<?php

namespace AerialShip\SteelMqBundle\Tests\Functional\Api;

use AerialShip\SteelMqBundle\Tests\Functional\AbstractFunctionTestCase;

class AuthFunctionalTest extends AbstractFunctionTestCase
{
    protected function setUp()
    {
        $this->loadUserAndProjectData();
    }

    public function testUserTokenQueryString()
    {
        $client = static::createClient();
        $client->request('GET', '/projects?token=userToken');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $json = json_decode($client->getResponse()->getContent());
        $this->assertTrue(is_array($json));
        $this->assertCount(4, $json);
        $arr = array();
        foreach ($json as $project) {
            $arr[$project->title] = 1;
        }
        $this->assertArrayHasKey('First Project', $arr);
        $this->assertArrayHasKey('Second Project', $arr);
        $this->assertArrayHasKey('Third Project', $arr);
        $this->assertArrayHasKey('Fourth Project', $arr);
    }

    public function testUserTokenHeader()
    {
        $client = static::createClient();
        $client->request('GET', '/projects', array(), array(), array(
            'HTTP_Authorization' => 'token userToken',
        ));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $json = json_decode($client->getResponse()->getContent());
        $this->assertTrue(is_array($json));
        $this->assertCount(4, $json);
        $arr = array();
        foreach ($json as $project) {
            $arr[$project->title] = 1;
        }
        $this->assertArrayHasKey('First Project', $arr);
        $this->assertArrayHasKey('Second Project', $arr);
        $this->assertArrayHasKey('Third Project', $arr);
        $this->assertArrayHasKey('Fourth Project', $arr);
    }

    public function testFirstProjectTokenQueryString()
    {
        $client = static::createClient();
        $client->request('GET', '/projects?token=userFirstProject');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $json = json_decode($client->getResponse()->getContent());
        $this->assertTrue(is_array($json));
        $this->assertCount(1, $json);
        $this->assertEquals('First Project', $json[0]->title);
    }

    public function testThirdProjectTokenQueryString()
    {
        $client = static::createClient();
        $client->request('GET', '/projects?token=guestThirdProject');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $json = json_decode($client->getResponse()->getContent());
        $this->assertTrue(is_array($json));
        $this->assertCount(1, $json);
        $this->assertEquals('Third Project', $json[0]->title);
    }

    public function testUnauthorizedWithoutToken()
    {
        $client = static::createClient();
        $client->request('GET', '/projects');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $json = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue(is_array($json));
        $this->assertCount(2, $json);

        $this->assertArrayHasKey('code', $json);
        $this->assertArrayHasKey('message', $json);

        $this->assertEquals('403', $json['code']);
        $this->assertEquals('Access Denied', $json['message']);
    }

    public function testUnauthorizedWithEmptyToken()
    {
        $client = static::createClient();
        $client->request('GET', '/projects?token=');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $json = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue(is_array($json));
        $this->assertCount(2, $json);

        $this->assertArrayHasKey('code', $json);
        $this->assertArrayHasKey('message', $json);

        $this->assertEquals('403', $json['code']);
        $this->assertEquals('Access Denied', $json['message']);
    }

    public function testUnauthorizedWithInvalidToken()
    {
        $client = static::createClient();
        $client->request('GET', '/projects?token=123123123123123123123123123');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $json = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue(is_array($json));
        $this->assertCount(2, $json);

        $this->assertArrayHasKey('code', $json);
        $this->assertArrayHasKey('message', $json);

        $this->assertEquals('403', $json['code']);
        $this->assertEquals('Access Denied', $json['message']);
    }
}
