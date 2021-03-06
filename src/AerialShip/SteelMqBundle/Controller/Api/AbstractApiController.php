<?php

namespace AerialShip\SteelMqBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;

class AbstractApiController extends FOSRestController
{
    /**
     * @param  array $extra
     * @return array
     */
    protected function getSuccessData($extra = array())
    {
        return array_merge(['success' => true], $extra);
    }

    /**
     * @param  mixed                                      $data
     * @param  array                                      $groups
     * @param  bool                                       $serializeNull
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleData($data, array $groups = null, $serializeNull = true)
    {
        $view = $this->view($data);

        $context = SerializationContext::create();
        $context->setSerializeNull($serializeNull);
        if ($groups) {
            $context->setGroups($groups);
        }

        $view->setSerializationContext($context);

        return parent::handleView($view);
    }
}
