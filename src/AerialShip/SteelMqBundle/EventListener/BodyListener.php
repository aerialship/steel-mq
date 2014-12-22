<?php

namespace AerialShip\SteelMqBundle\EventListener;

use FOS\RestBundle\Decoder\DecoderProviderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BodyListener
{
    /** @var DecoderProviderInterface */
    private $decoderProvider;

    /**
     * @param DecoderProviderInterface $decoderProvider
     */
    public function __construct(DecoderProviderInterface $decoderProvider)
    {
        $this->decoderProvider = $decoderProvider;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $decoderId = $request->get('_decoder');
        if (empty($decoderId) || 'none' == $decoderId) {
            return;
        }

        $method = $request->getMethod();

        if (!count($request->request->all())
            && in_array($method, array('POST', 'PUT', 'PATCH', 'DELETE'))
        ) {
            $content = $request->getContent();
            if (empty($content)) {
                return;
            }

            if (!$this->decoderProvider->supports($decoderId)) {
                throw new HttpException(500, sprintf('Unsupported decoder "%s"', $decoderId));
            }

            $decoder = $this->decoderProvider->getDecoder($decoderId);
            $data = $decoder->decode($content);
            if (is_array($data)) {
                $request->request = new ParameterBag($data);
            } else {
                throw new BadRequestHttpException(sprintf('Invalid %s message received', $decoderId));
            }
        }
    }
}
