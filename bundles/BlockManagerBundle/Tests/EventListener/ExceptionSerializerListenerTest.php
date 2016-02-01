<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener;
use Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use Exception;

class ExceptionSerializerListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $serializerMock = $this->getMock(SerializerInterface::class);
        $eventListener = new ExceptionSerializerListener($serializerMock);

        self::assertEquals(
            array(KernelEvents::EXCEPTION => 'onException'),
            $eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnException()
    {
        $exception = new Exception();

        $serializerMock = $this->getMock(SerializerInterface::class);
        $serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with(
                $this->equalTo($exception),
                $this->equalTo('json')
            )
            ->will(
                $this->returnValue('serialized content')
            );

        $eventListener = new ExceptionSerializerListener($serializerMock);

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $eventListener->onException($event);

        self::assertInstanceOf(
            JsonResponse::class,
            $event->getResponse()
        );

        self::assertEquals(
            'serialized content',
            $event->getResponse()->getContent()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnExceptionWithNoApiRequest()
    {
        $serializerMock = $this->getMock(SerializerInterface::class);
        $eventListener = new ExceptionSerializerListener($serializerMock);

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Exception()
        );

        $eventListener->onException($event);

        self::assertNull($event->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnExceptionInSubRequest()
    {
        $serializerMock = $this->getMock(SerializerInterface::class);
        $eventListener = new ExceptionSerializerListener($serializerMock);

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Exception()
        );

        $eventListener->onException($event);

        self::assertNull($event->getResponse());
    }
}
