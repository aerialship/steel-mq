<?php

namespace AerialShip\SteelMqBundle\Tests\Unit\Service\Sharing;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\ProjectRole;
use AerialShip\SteelMqBundle\Entity\User;
use AerialShip\SteelMqBundle\Services\Sharing\ProjectShareService;

class ProjectShareServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Project */
    protected $project;

    /** @var  ProjectRole */
    protected $owner;

    protected function setUp()
    {
        $this->project = new Project();
        $this->owner = (new ProjectRole())
            ->setProject($this->project)
            ->setRoles(array(ProjectRole::PROJECT_ROLE_OWNER))
            ->setUser(
                (new User())
                    ->setEmail('owner@aerialship.com')
            );
        $this->project->getProjectRoles()->add($this->owner);

        $this->project->getProjectRoles()->add(
            (new ProjectRole())
                ->setProject($this->project)
                ->setUser(
                    (new User())
                        ->setEmail('member@aerialship.com')
                )
        );
        $this->project->getProjectRoles()->add(
            (new ProjectRole())
                ->setProject($this->project)
                ->setRoles(array(ProjectRole::PROJECT_ROLE_SUBSCRIBE, ProjectRole::PROJECT_ROLE_QUEUE, ProjectRole::PROJECT_ROLE_SHARE))
                ->setUser(
                    (new User())
                        ->setEmail('admin@aerialship.com')
                )
        );
        $this->project->getProjectRoles()->add(
            (new ProjectRole())
                ->setProject($this->project)
                ->setRoles(array(ProjectRole::PROJECT_ROLE_SHARE))
                ->setUser(
                    (new User())
                        ->setEmail('share@aerialship.com')
                )
        );
    }

    public function testShareInfo()
    {
        $projectRoleRepositoryMock = $this->getProjectRoleRepositoryMock();
        $userRepositoryMock = $this->getUserRepositoryMock();

        $service = new ProjectShareService($projectRoleRepositoryMock, $userRepositoryMock);

        $info = $service->getShareInfo($this->project);

        $expected = array(
            array(
                $this->owner,
            ),
            array(
                'roles' => array(
                    array(
                        'email' => 'member@aerialship.com',
                        'roles' => array(ProjectRole::PROJECT_ROLE_DEFAULT),
                    ),
                    array(
                        'email' => 'admin@aerialship.com',
                        'roles' => array(
                            ProjectRole::PROJECT_ROLE_SUBSCRIBE,
                            ProjectRole::PROJECT_ROLE_QUEUE,
                            ProjectRole::PROJECT_ROLE_SHARE,
                            ProjectRole::PROJECT_ROLE_DEFAULT,
                        ),
                    ),
                    array(
                        'email' => 'share@aerialship.com',
                        'roles' => array(
                            ProjectRole::PROJECT_ROLE_SHARE,
                            ProjectRole::PROJECT_ROLE_DEFAULT,
                        ),
                    ),
                ),
            ),
        );

        $this->assertEquals($expected, $info);
    }

    public function testSetSharing()
    {
        $data = array(
            array(
                'email' => 'member@aerialship.com',
                'roles' => array(ProjectRole::PROJECT_ROLE_QUEUE),
            ),
            array(
                'email' => 'admin@aerialship.com',
                'roles' => array(ProjectRole::PROJECT_ROLE_SUBSCRIBE),
            ),
            array(
                'email' => 'new@aerialship.com',
                'roles' => array(ProjectRole::PROJECT_ROLE_SHARE),
            ),
        );

        $savedRoles = array();
        $deletedRoles = array();

        $projectRoleRepositoryMock = $this->getProjectRoleRepositoryMock();
        $projectRoleRepositoryMock->expects($this->any())
            ->method('save')
            ->will($this->returnCallback(function (ProjectRole $pr) use (&$savedRoles) {
                $savedRoles[$pr->getUser()->getEmail()] = 1;
            }));
        $projectRoleRepositoryMock->expects($this->any())
            ->method('delete')
            ->will($this->returnCallback(function (ProjectRole $pr) use (&$deletedRoles) {
                $deletedRoles[$pr->getUser()->getEmail()] = 1;
            }));

        $userRepositoryMock = $this->getUserRepositoryMock();
        $userRepositoryMock->expects($this->once())
            ->method('getByUsername')
            ->with('new@aerialship.com')
            ->willReturn(null);
        $userRepositoryMock->expects($this->once())
            ->method('save');

        $service = new ProjectShareService($projectRoleRepositoryMock, $userRepositoryMock);

        $service->setSharing($this->project, $data);

        $this->assertCount(3, $savedRoles);
        $this->assertArrayHasKey('member@aerialship.com', $savedRoles);
        $this->assertArrayHasKey('admin@aerialship.com', $savedRoles);
        $this->assertArrayHasKey('new@aerialship.com', $savedRoles);

        $this->assertCount(1, $deletedRoles);
        $this->assertArrayHasKey('share@aerialship.com', $deletedRoles);

        foreach ($this->project->getProjectRoles() as $role) {
            switch ($role->getUser()->getEmail()) {
                case 'owner@aerialship.com':
                    $this->assertEquals(array(ProjectRole::PROJECT_ROLE_OWNER, ProjectRole::PROJECT_ROLE_DEFAULT), $role->getRoles());
                    break;
                case 'member@aerialship.com':
                    $this->assertEquals(array(ProjectRole::PROJECT_ROLE_QUEUE, ProjectRole::PROJECT_ROLE_DEFAULT), $role->getRoles());
                    break;
                case 'admin@aerialship.com':
                    $this->assertEquals(array(ProjectRole::PROJECT_ROLE_SUBSCRIBE, ProjectRole::PROJECT_ROLE_DEFAULT), $role->getRoles());
                    break;
                case 'new@aerialship.com':
                    $this->assertEquals(array(ProjectRole::PROJECT_ROLE_SHARE, ProjectRole::PROJECT_ROLE_DEFAULT), $role->getRoles());
                    break;
                default:
                    throw new \RuntimeException(sprintf("Unexpected project role '%s'", $role->getUser()->getEmail()));
            }
        }
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\AerialShip\SteelMqBundle\Model\Repository\ProjectRoleRepositoryInterface
     */
    private function getProjectRoleRepositoryMock()
    {
        return $this->getMock('AerialShip\SteelMqBundle\Model\Repository\ProjectRoleRepositoryInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\AerialShip\SteelMqBundle\Model\Repository\UserRepositoryInterface
     */
    private function getUserRepositoryMock()
    {
        return $this->getMock('AerialShip\SteelMqBundle\Model\Repository\UserRepositoryInterface');
    }
}
