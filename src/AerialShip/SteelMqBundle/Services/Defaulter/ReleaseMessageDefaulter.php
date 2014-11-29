<?php

namespace AerialShip\SteelMqBundle\Services\Defaulter;

use AerialShip\SteelMqBundle\Entity\Queue;

class ReleaseMessageDefaulter
{
    public function setDefaults(Queue $queue, array &$data)
    {
        $data = array_merge(
            array(
                'delay' => $queue->getDelay(),
            ),
            array_filter($data)
        );
    }
}
