<?php

namespace AerialShip\SteelMqBundle\Entity\Repository;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Model\Repository\ProjectRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class ProjectRepository extends EntityRepository implements ProjectRepositoryInterface
{
    /**
     * @param $callback
     * @return mixed
     */
    public function transactional($callback)
    {
        return $this->_em->transactional($callback);
    }

    /**
     * @param  Project $project
     * @param  bool    $flush
     * @return void
     */
    public function save(Project $project, $flush = true)
    {
        $this->_em->persist($project);
        if ($flush) {
            $this->_em->flush();
        }
    }

}
