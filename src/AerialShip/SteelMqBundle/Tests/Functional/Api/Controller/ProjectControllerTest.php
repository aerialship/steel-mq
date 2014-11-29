<?php

namespace AerialShip\SteelMqBundle\Tests\Functional\Api\Controller;

use AerialShip\SteelMqBundle\Tests\Functional\AbstractFunctionTestCase;

class ProjectControllerTest extends AbstractFunctionTestCase
{
    protected function setUp()
    {
        $this->loadProjectData();
    }

    public function testListAction()
    {
        $token = 'userToken';

        $client = static::createClient();
        $client->request('GET', sprintf('projects?token=%s', $token));
        $response = $client->getResponse();
        $this->assertJsonResponse($response);

        $json = json_decode($response->getContent(), true);

        $this->assertTrue(is_array($json));
        $this->assertCount(4, $json);
        $arr = array();
        foreach ($json as $project) {
            $arr[$project['title']] = 1;
        }
        $this->assertArrayHasKey('First Project', $arr);
        $this->assertArrayHasKey('Second Project', $arr);
        $this->assertArrayHasKey('Third Project', $arr);
        $this->assertArrayHasKey('Fourth Project', $arr);
    }

    public function testCreateAction()
    {
        $token = 'userToken';

        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('projects?token=%s', $token),
            array(
                'project' => array(
                    'title' => $expectedTitle = 'My New Project',
                ),
            )
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response);

        $json = json_decode($response->getContent(), true);

        $this->assertTrue(is_array($json));

        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('success', $json);
        $this->assertTrue($json['success']);

        $projectId = $json['id'];

        $project = $this->getProjectRepository()->find($projectId);
        $this->assertNotNull($project);
        $this->assertEquals($expectedTitle, $project->getTitle());
    }

    public function testCreateEmptyPost()
    {
        $token = 'userToken';

        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('projects?token=%s', $token)
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 400);

        $json = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('code', $json);
        $this->assertArrayHasKey('message', $json);
        $this->assertArrayHasKey('errors', $json);
        $this->assertArrayHasKey('children', $json['errors']);
        $this->assertArrayHasKey('title', $json['errors']['children']);
        $this->assertArrayHasKey('errors', $json['errors']['children']['title']);
        $this->assertCount(1, $json['errors']['children']['title']['errors']);

        $this->assertEquals(400, $json['code']);
        $this->assertEquals('Validation Failed', $json['message']);
        $this->assertEquals('This value should not be blank.', $json['errors']['children']['title']['errors'][0]);
    }
}
