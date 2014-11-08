<?php

namespace AerialShip\SteelMqBundle\DataFixtures\Orm;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\ProjectRole;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ProjectData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $project = new Project();
        $project->setTitle('First Project')
            ->setOwner($this->getReference('user-user'));
        $manager->persist($project);
        $manager->flush();
        $this->addReference('project-one', $project);

        $projectRole = new ProjectRole();
        $projectRole->setProject($project)
            ->setUser($this->getReference('user-user'))
            ->setAccessToken('userFirstProject')
            ->setRoles(array(ProjectRole::PROJECT_ROLE_OWNER));
        $manager->persist($projectRole);
        $manager->flush();

        $projectRole = new ProjectRole();
        $projectRole->setProject($project)
            ->setUser($this->getReference('guest-user'))
            ->setAccessToken('guestFirstProject')
            ->setRoles(array(ProjectRole::PROJECT_ROLE_DEFAULT));
        $manager->persist($projectRole);
        $manager->flush();

        // ----------------------------------------------------------

        $project = new Project();
        $project->setTitle('Second Project')
            ->setOwner($this->getReference('user-user'));
        $manager->persist($project);
        $manager->flush();
        $this->addReference('project-two', $project);

        $projectRole = new ProjectRole();
        $projectRole->setProject($project)
            ->setUser($this->getReference('user-user'))
            ->setAccessToken('userSecondProject')
            ->setRoles(array(ProjectRole::PROJECT_ROLE_OWNER));
        $manager->persist($projectRole);
        $manager->flush();

        $projectRole = new ProjectRole();
        $projectRole->setProject($project)
            ->setUser($this->getReference('guest-user'))
            ->setAccessToken('guestSecondProject')
            ->setRoles(array(ProjectRole::PROJECT_ROLE_QUEUE));
        $manager->persist($projectRole);
        $manager->flush();

        // ----------------------------------------------------------

        $project = new Project();
        $project->setTitle('Third Project')
            ->setOwner($this->getReference('user-user'));
        $manager->persist($project);
        $manager->flush();
        $this->addReference('project-three', $project);

        $projectRole = new ProjectRole();
        $projectRole->setProject($project)
            ->setUser($this->getReference('user-user'))
            ->setAccessToken('userThirdProject')
            ->setRoles(array(ProjectRole::PROJECT_ROLE_OWNER));
        $manager->persist($projectRole);
        $manager->flush();

        $projectRole = new ProjectRole();
        $projectRole->setProject($project)
            ->setUser($this->getReference('guest-user'))
            ->setAccessToken('guestThirdProject')
            ->setRoles(array(ProjectRole::PROJECT_ROLE_SUBSCRIBE));
        $manager->persist($projectRole);
        $manager->flush();

        // ----------------------------------------------------------

        $project = new Project();
        $project->setTitle('Fourth Project')
            ->setOwner($this->getReference('user-user'));
        $manager->persist($project);
        $manager->flush();
        $this->addReference('project-four', $project);

        $projectRole = new ProjectRole();
        $projectRole->setProject($project)
            ->setUser($this->getReference('user-user'))
            ->setAccessToken('userFourthProject')
            ->setRoles(array(ProjectRole::PROJECT_ROLE_OWNER));
        $manager->persist($projectRole);
        $manager->flush();

        $projectRole = new ProjectRole();
        $projectRole->setProject($project)
            ->setUser($this->getReference('guest-user'))
            ->setAccessToken('guestFourthProject')
            ->setRoles(array(ProjectRole::PROJECT_ROLE_SHARE));
        $manager->persist($projectRole);
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 20;
    }

}
