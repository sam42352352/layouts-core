<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Collection;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemParamConverter;
use PHPUnit\Framework\TestCase;

final class ItemParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $collectionServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemParamConverter
     */
    private $paramConverter;

    public function setUp()
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->paramConverter = new ItemParamConverter($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName()
    {
        $this->assertEquals(['itemId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        $this->assertEquals('item', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        $this->assertEquals(APIItem::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemParamConverter::loadValue
     */
    public function testLoadValue()
    {
        $item = new Item();

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadItem')
            ->with($this->equalTo(42))
            ->will($this->returnValue($item));

        $this->assertEquals(
            $item,
            $this->paramConverter->loadValue(
                [
                    'itemId' => 42,
                    'status' => 'published',
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemParamConverter::loadValue
     */
    public function testLoadValueDraft()
    {
        $item = new Item();

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadItemDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($item));

        $this->assertEquals(
            $item,
            $this->paramConverter->loadValue(
                [
                    'itemId' => 42,
                    'status' => 'draft',
                ]
            )
        );
    }
}
