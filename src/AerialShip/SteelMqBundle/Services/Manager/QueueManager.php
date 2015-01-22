<?php

namespace AerialShip\SteelMqBundle\Services\Manager;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\ProjectRole;
use AerialShip\SteelMqBundle\Entity\Queue;
use AerialShip\SteelMqBundle\Model\Repository\QueueRepositoryInterface;
use AerialShip\SteelMqBundle\Services\Defaulter\QueueDefaulter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;

class QueueManager
{
    /** @var SecurityContextInterface */
    protected $securityContext;

    /** @var QueueRepositoryInterface */
    protected $queueRepository;

    /** @var QueueDefaulter */
    protected $queueDefaulter;

    /**
     * @param QueueRepositoryInterface $queueRepository
     * @param SecurityContextInterface $securityContext
     * @param QueueDefaulter           $queueDefaulter
     */
    public function __construct(QueueRepositoryInterface $queueRepository, SecurityContextInterface $securityContext, QueueDefaulter $queueDefaulter)
    {
        $this->queueRepository = $queueRepository;
        $this->securityContext = $securityContext;
        $this->queueDefaulter = $queueDefaulter;
    }

    /**
     * @param  Project $project
     * @param  int     $limit
     * @param  int     $offset
     * @param  bool    $security
     * @return Queue[]
     */
    public function getList(Project $project, $limit = 100, $offset = 0, $security = true)
    {
        if ($security &&
            false === $this->securityContext->isGranted(ProjectRole::PROJECT_ROLE_DEFAULT, $project)
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
     * @param Queue   $queue
     */
    public function create(Project $project, Queue $queue)
    {
        $this->queueDefaulter->setDefaults($queue);
        $queue->setProject($project);

        $this->queueRepository->save($queue);
    }

    /**
     * @param Project $project
     * @param Queue   $queue
     */
    public function update(Project $project, Queue $queue)
    {
        $this->create($project, $queue);
    }

    /**
     * @param Queue $queue
     */
    public function delete(Queue $queue)
    {
        $this->queueRepository->delete($queue);
    }

    public function clear(Queue $queue)
    {
        $this->queueRepository->transactional(function () use ($queue) {
            $deletedCount = $this->queueRepository->clearQueue($queue);
            $queue->setDeletedCount($queue->getDeletedCount() + $deletedCount);
            $this->queueRepository->save($queue);
        });
    }
}
