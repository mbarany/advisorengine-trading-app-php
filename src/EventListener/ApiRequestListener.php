<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ApiRequestListener
{
    const API_V1_PREFIX = '/^\/api\/v1/';

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        // Force JSON for API
        if (preg_match(self::API_V1_PREFIX, $event->getRequest()->getRequestUri()) === 1) {
            $event->getRequest()->setRequestFormat('json');
        }
    }
}
