<?php

namespace AerialShip\SteelMqBundle\Services\Defaulter;

use AerialShip\SteelMqBundle\Entity\Queue;

class GetMessageDefaulter
{
    public function setDefaults(Queue $queue, array &$data)
    {
        $data = array_merge(
            array(
                'limit' => 1,
                'timeout' => $queue->getTimeout(),
                'delete' => false,
            ),
            array_filter($data)
        );
    }
}
