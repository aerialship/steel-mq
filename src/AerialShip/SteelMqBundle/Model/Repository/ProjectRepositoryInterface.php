<?php

namespace AerialShip\SteelMqBundle\Model\Repository;

use AerialShip\SteelMqBundle\Entity\Project;

interface ProjectRepositoryInterface
{
    /**
     * @param $callback
     * @return mixed
     */
    public function transactional($callback);

    /**
     * @param  Project $project
     * @param  bool    $flush
     * @return void
     */
    public function save(Project $project, $flush = true);

    /**
     * @param  array        $criteria
     * @param  array        $orderBy
     * @return Project|null
     */
    public function findOneBy(array $criteria, array $orderBy = null);
}
