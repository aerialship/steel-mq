<?php

namespace AerialShip\SteelMqBundle\Services\Manager;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\ProjectRole;
use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Services\Defaulter\QueueDefaulter;
use AerialShip\SteelMqBundle\Services\UserProvider;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;

class QueueManager
{
    /** @var SecurityContextInterface */
    protected $securityContext;

    /** @var EntityManager */
    protected $entityManager;

    /** @var QueueDefaulter */
    protected $queueDefaulter;

    /**
     * @param EntityManager $entityManager
     * @param SecurityContextInterface $securityContext
     * @param QueueDefaulter $queueDefaulter
     */
    public function __construct(EntityManager $entityManager, SecurityContextInterface $securityContext, QueueDefaulter $queueDefaulter)
    {
        $this->entityManager = $entityManager;
        $this->securityContext = $securityContext;
        $this->queueDefaulter = $queueDefaulter;
    }

    /**
     * @param Project $project
     * @param int $limit
     * @param int $offset
     * @param bool $security
     * @return Queue[]
     */
    public function getList(Project $project, $limit = 100, $offset = 0, $security = true)
    {
        if ($security &&
            false == $this->securityContext->isGranted(ProjectRole::PROJECT_ROLE_DEFAULT, $project)
        ) {
            throw new AccessDeniedException();
        };

        $result = array();
        foreach ($project->getQueues()->slice($offset, $limit) as $queue) {
            $result[] = $queue;
        }

        return $result;
    }

    /**
     * @param Project $project
     * @param Queue $queue
     */
    public function create(Project $project, Queue $queue)
    {
        $this->queueDefaulter->setDefaults($queue);
        $queue->setProject($project);

        $this->entityManager->persist($queue);
        $this->entityManager->flush();
    }

    /**
     * @param Project $project
     * @param Queue $queue
     */
    public function update(Project $project, Queue $queue)
    {
        $this->create($project, $queue);
    }
}
