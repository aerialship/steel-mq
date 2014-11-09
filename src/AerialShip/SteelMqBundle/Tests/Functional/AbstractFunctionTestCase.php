<?php

namespace AerialShip\SteelMqBundle\Tests\Functional;

use AerialShip\SteelMqBundle\DataFixtures\Orm\MessageData;
use AerialShip\SteelMqBundle\DataFixtures\Orm\ProjectData;
use AerialShip\SteelMqBundle\DataFixtures\Orm\QueueData;
use AerialShip\SteelMqBundle\DataFixtures\Orm\UserData;
use AerialShip\SteelMqBundle\Model\Repository\MessageRepositoryInterface;
use AerialShip\SteelMqBundle\Model\Repository\ProjectRepositoryInterface;
use AerialShip\SteelMqBundle\Model\Repository\QueueRepositoryInterface;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class AbstractFunctionTestCase extends WebTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
        static::ensureKernelShutdown();
        static::$kernel = null;
    }

    /**
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    protected function getBootedKernel()
    {
        if (false == static::$kernel) {
            $this->bootKernel();
        }

        return static::$kernel;
    }

    /**
     * @param  string $name
     * @return object
     */
    protected function getService($name)
    {
        return $this->getBootedKernel()->getContainer()->get($name);
    }

    /**
     * @param  string $name
     * @return bool
     */
    protected function hasService($name)
    {
        return $this->getBootedKernel()->getContainer()->has($name);
    }

    /**
     * @return ProjectRepositoryInterface
     */
    protected function getProjectRepository()
    {
        $result = $this->getService('doctrine')
            ->getManager()
            ->getRepository('AerialShipSteelMqBundle:Project');

        if ($result instanceof ProjectRepositoryInterface) {
            return $result;
        }

        throw new \LogicException('Expected ProjectRepositoryInterface');
    }

    /**
     * @return QueueRepositoryInterface
     */
    protected function getQueueRepository()
    {
        $result = $this->getService('doctrine')
            ->getManager()
            ->getRepository('AerialShipSteelMqBundle:Queue');

        if ($result instanceof QueueRepositoryInterface) {
            return $result;
        }

        throw new \LogicException('Expected QueueRepositoryInterface');
    }

    /**
     * @return MessageRepositoryInterface
     */
    protected function getMessageRepository()
    {
        $result = $this->getService('doctrine')
            ->getManager()
            ->getRepository('AerialShipSteelMqBundle:Message');

        if ($result instanceof MessageRepositoryInterface) {
            return $result;
        }

        throw new \LogicException('Expected MessageRepositoryInterface');
    }

    /**
     * @return EntityManager
     */
    protected function getEm()
    {
        return $this->getService('doctrine')->getManager();
    }

    /**
     * @param Loader $loader
     */
    protected function loadFixtures(Loader $loader)
    {
        foreach ($loader->getFixtures() as $fixture) {
            if ($fixture instanceof ContainerAwareInterface) {
                $fixture->setContainer($this->getBootedKernel()->getContainer());
            }
        }
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->getEm(), $purger);
        $executor->execute($loader->getFixtures());
    }

    protected function loadUserData()
    {
        $loader = new Loader();
        $loader->addFixture(new UserData());
        $loader->addFixture(new ProjectData());
        $loader->addFixture(new QueueData());
        $this->loadFixtures($loader);
    }

    protected function loadProjectData()
    {
        $loader = new Loader();
        $loader->addFixture(new UserData());
        $loader->addFixture(new ProjectData());
        $loader->addFixture(new QueueData());
        $this->loadFixtures($loader);
    }

    protected function loadQueueData()
    {
        $loader = new Loader();
        $loader->addFixture(new UserData());
        $loader->addFixture(new ProjectData());
        $loader->addFixture(new QueueData());
        $this->loadFixtures($loader);
    }

    protected function loadMessageData()
    {
        $loader = new Loader();
        $loader->addFixture(new UserData());
        $loader->addFixture(new ProjectData());
        $loader->addFixture(new QueueData());
        $loader->addFixture(new MessageData());
        $this->loadFixtures($loader);
    }

    /**
     * @param  string $route
     * @param  array  $params
     * @param  bool   $referenceType
     * @return string
     */
    protected function generateUrl($route, $params = array(), $referenceType = RouterInterface::ABSOLUTE_PATH)
    {
        return $this->getBootedKernel()->getContainer()->get('router')->generate($route, $params, $referenceType);
    }

    /**
     * @param Response $response
     * @param int      $statusCode
     */
    protected function assertJsonResponse(Response $response, $statusCode = 200)
    {
        $this->assertEquals(
            $statusCode,
            $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
    }
}
