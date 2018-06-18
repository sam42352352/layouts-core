<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\HttpCache;

use Netgen\BlockManager\View\View\BlockView;
use Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class CacheableViewListenerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->listener = new CacheableViewListener();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        $this->assertSame(
            [KernelEvents::RESPONSE => ['onKernelResponse', -255]],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onKernelResponse
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::setUpCachingHeaders
     */
    public function testOnKernelResponse(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView();
        $blockView->setSharedMaxAge(42);

        $request->attributes->set('ngbmView', $blockView);

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->listener->onKernelResponse($event);

        $this->assertSame(42, $event->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onKernelResponse
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::setUpCachingHeaders
     */
    public function testOnKernelResponseWithDisabledCache(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView();
        $blockView->setIsCacheable(false);
        $blockView->setSharedMaxAge(42);

        $request->attributes->set('ngbmView', $blockView);

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->listener->onKernelResponse($event);

        $this->assertNull($event->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onKernelResponse
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::setUpCachingHeaders
     */
    public function testOnKernelResponseWithExistingHeaders(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView();
        $blockView->setSharedMaxAge(42);

        $request->attributes->set('ngbmView', $blockView);

        $response = new Response();
        $response->setSharedMaxAge(41);

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $response
        );

        $this->listener->onKernelResponse($event);

        $this->assertSame(41, $event->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onKernelResponse
     */
    public function testOnKernelResponseWithSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView();
        $blockView->setSharedMaxAge(42);

        $request->attributes->set('ngbmView', $blockView);

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Response()
        );

        $this->listener->onKernelResponse($event);

        $this->assertNull($event->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onKernelResponse
     */
    public function testOnKernelResponseWithoutSupportedValue(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->listener->onKernelResponse($event);

        $this->assertNull($event->getResponse()->getMaxAge());
    }
}
