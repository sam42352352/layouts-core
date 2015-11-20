<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Netgen\BlockManager\API\Exception\NotFoundException;
use InvalidArgumentException;

class ExceptionConversionListener implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(KernelEvents::EXCEPTION => array('onException', 10));
    }

    /**
     * Converts exceptions to Symfony HTTP exceptions.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     */
    public function onException(GetResponseForExceptionEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $exception = $event->getException();
        if ($exception instanceof NotFoundException) {
            $exceptionClass = 'Symfony\Component\HttpKernel\Exception\NotFoundHttpException';
        } elseif ($exception instanceof InvalidArgumentException) {
            $exceptionClass = 'Symfony\Component\HttpKernel\Exception\BadRequestHttpException';
        } else {
            $exceptionClass = 'Netgen\Bundle\BlockManagerBundle\Exception\InternalServerErrorHttpException';
        }

        $convertedException = new $exceptionClass(
            $exception->getMessage(),
            $exception,
            $exception->getCode()
        );

        $event->setException($convertedException);
    }
}
