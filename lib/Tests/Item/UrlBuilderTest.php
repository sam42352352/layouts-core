<?php

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Tests\Item\Stubs\ValueUrlBuilder;
use Netgen\BlockManager\Item\UrlBuilder;
use Netgen\BlockManager\Item\Item;
use PHPUnit\Framework\TestCase;

class UrlBuilderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\UrlBuilderInterface
     */
    protected $urlBuilder;

    public function setUp()
    {
        $this->urlBuilder = new UrlBuilder(
            array('value' => new ValueUrlBuilder())
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\UrlBuilder::__construct
     * @covers \Netgen\BlockManager\Item\UrlBuilder::getUrl
     */
    public function testGetUrl()
    {
        $this->assertEquals(
            '/item-url',
            $this->urlBuilder->getUrl(
                new Item(array('valueType' => 'value'))
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\UrlBuilder::getUrl
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testGetUrlWithNoUrlBuilder()
    {
        $this->urlBuilder->getUrl(
            new Item(array('valueType' => 'unknown'))
        );
    }
}
