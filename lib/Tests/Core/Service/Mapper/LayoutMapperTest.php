<?php

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Page\LayoutInfo;
use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\API\Values\Page\Zone as APIZone;
use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
use Netgen\BlockManager\Persistence\Values\Page\Zone;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use DateTime;

abstract class LayoutMapperTest extends MapperTest
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    protected $layoutMapper;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->layoutMapper = $this->createLayoutMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapZone
     */
    public function testMapZone()
    {
        $persistenceZone = new Zone(
            array(
                'identifier' => 'right',
                'layoutId' => 1,
                'status' => APILayout::STATUS_PUBLISHED,
                'linkedLayoutId' => 24,
                'linkedZoneIdentifier' => 'top',
            )
        );

        $zone = $this->layoutMapper->mapZone($persistenceZone);

        self::assertInstanceOf(APIZone::class, $zone);
        self::assertEquals('right', $zone->getIdentifier());
        self::assertEquals(1, $zone->getLayoutId());
        self::assertEquals(APILayout::STATUS_PUBLISHED, $zone->getStatus());
        self::assertEquals(24, $zone->getLinkedLayoutId());
        self::assertEquals('top', $zone->getLinkedZoneIdentifier());

        self::assertNotEmpty($zone->getBlocks());

        $position = 0;
        foreach ($zone->getBlocks() as $block) {
            self::assertEquals($position, $block->getPosition());
            self::assertInstanceOf(APIBlock::class, $block);
            ++$position;
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapLayout
     * @covers \Netgen\BlockManager\Core\Service\Mapper\Mapper::createDateTime
     */
    public function testMapLayout()
    {
        $persistenceLayout = new Layout(
            array(
                'id' => 1,
                'type' => '4_zones_a',
                'name' => 'My layout',
                'created' => 1447065813,
                'modified' => 1447065813,
                'status' => APILayout::STATUS_PUBLISHED,
                'shared' => true,
            )
        );

        $layout = $this->layoutMapper->mapLayout($persistenceLayout);

        self::assertInstanceOf(APILayout::class, $layout);
        self::assertEquals(1, $layout->getId());
        self::assertEquals('4_zones_a', $layout->getType());
        self::assertEquals('My layout', $layout->getName());
        self::assertInstanceOf(DateTime::class, $layout->getCreated());
        self::assertEquals(1447065813, $layout->getCreated()->getTimestamp());
        self::assertInstanceOf(DateTime::class, $layout->getModified());
        self::assertEquals(1447065813, $layout->getModified()->getTimestamp());
        self::assertEquals(APILayout::STATUS_PUBLISHED, $layout->getStatus());
        self::assertTrue($layout->isShared());

        self::assertNotEmpty($layout->getZones());

        foreach ($layout->getZones() as $zone) {
            self::assertInstanceOf(APIZone::class, $zone);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapLayoutInfo
     * @covers \Netgen\BlockManager\Core\Service\Mapper\Mapper::createDateTime
     */
    public function testMapLayoutInfo()
    {
        $persistenceLayout = new Layout(
            array(
                'id' => 1,
                'type' => '4_zones_a',
                'name' => 'My layout',
                'created' => 1447065813,
                'modified' => 1447065813,
                'status' => APILayout::STATUS_PUBLISHED,
                'shared' => true,
            )
        );

        $layout = $this->layoutMapper->mapLayoutInfo($persistenceLayout);

        self::assertInstanceOf(LayoutInfo::class, $layout);
        self::assertEquals(1, $layout->getId());
        self::assertEquals('4_zones_a', $layout->getType());
        self::assertEquals('My layout', $layout->getName());
        self::assertInstanceOf(DateTime::class, $layout->getCreated());
        self::assertEquals(1447065813, $layout->getCreated()->getTimestamp());
        self::assertInstanceOf(DateTime::class, $layout->getModified());
        self::assertEquals(1447065813, $layout->getModified()->getTimestamp());
        self::assertEquals(APILayout::STATUS_PUBLISHED, $layout->getStatus());
        self::assertTrue($layout->isShared());
    }
}
