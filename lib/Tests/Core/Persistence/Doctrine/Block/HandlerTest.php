<?php

namespace Netgen\BlockManager\Tests\Core\Persistence\Doctrine\Block;

use Netgen\BlockManager\Tests\Core\Persistence\Doctrine\TestCase;
use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Page\Block;

class HandlerTest extends \PHPUnit_Framework_TestCase
{
    use TestCase;

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::loadBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::__construct
     */
    public function testLoadBlock()
    {
        $handler = $this->createBlockHandler();

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'zoneId' => 2,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'some_param' => 'some_value',
                    ),
                    'viewType' => 'default',
                    'name' => 'My block',
                )
            ),
            $handler->loadBlock(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::loadBlock
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadBlockThrowsNotFoundException()
    {
        $handler = $this->createBlockHandler();
        $handler->loadBlock(PHP_INT_MAX);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::loadZoneBlocks
     */
    public function testLoadZoneBlocks()
    {
        $handler = $this->createBlockHandler();

        self::assertEquals(
            array(
                new Block(
                    array(
                        'id' => 1,
                        'zoneId' => 2,
                        'definitionIdentifier' => 'paragraph',
                        'parameters' => array(
                            'some_param' => 'some_value',
                        ),
                        'viewType' => 'default',
                        'name' => 'My block',
                    )
                ),
                new Block(
                    array(
                        'id' => 2,
                        'zoneId' => 2,
                        'definitionIdentifier' => 'title',
                        'parameters' => array(
                            'other_param' => 'other_value',
                        ),
                        'viewType' => 'small',
                        'name' => 'My other block',
                    )
                ),
            ),
            $handler->loadZoneBlocks(2)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::loadZoneBlocks
     */
    public function testLoadZoneBlocksForNonExistingZone()
    {
        $handler = $this->createBlockHandler();
        self::assertEquals(array(), $handler->loadZoneBlocks(PHP_INT_MAX));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::createBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::createBlockInsertQuery
     */
    public function testCreateBlock()
    {
        $handler = $this->createBlockHandler();

        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->setParameter('a_param', 'A value');

        self::assertEquals(
            new Block(
                array(
                    'id' => 5,
                    'zoneId' => 3,
                    'definitionIdentifier' => 'new_block',
                    'parameters' => array(
                        'a_param' => 'A value',
                    ),
                    'viewType' => 'large',
                    'name' => 'My block',
                )
            ),
            $handler->createBlock($blockCreateStruct, 3)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::updateBlock
     */
    public function testUpdateBlock()
    {
        $handler = $this->createBlockHandler();

        $blockUpdateStruct = new BlockUpdateStruct();
        $blockUpdateStruct->name = 'My block';
        $blockUpdateStruct->viewType = 'large';
        $blockUpdateStruct->setParameter('a_param', 'A value');

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'zoneId' => 2,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'a_param' => 'A value',
                    ),
                    'viewType' => 'large',
                    'name' => 'My block',
                )
            ),
            $handler->updateBlock(1, $blockUpdateStruct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::copyBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::createBlockInsertQuery
     */
    public function testCopyBlock()
    {
        $handler = $this->createBlockHandler();

        self::assertEquals(
            new Block(
                array(
                    'id' => 5,
                    'zoneId' => 2,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'some_param' => 'some_value',
                    ),
                    'viewType' => 'default',
                    'name' => 'My block',
                )
            ),
            $handler->copyBlock(1, 2)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::copyBlock
     */
    public function testCopyBlockToDifferentZone()
    {
        $handler = $this->createBlockHandler();

        self::assertEquals(
            new Block(
                array(
                    'id' => 5,
                    'zoneId' => 3,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'some_param' => 'some_value',
                    ),
                    'viewType' => 'default',
                    'name' => 'My block',
                )
            ),
            $handler->copyBlock(1, 3)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::moveBlock
     */
    public function testMoveBlock()
    {
        $handler = $this->createBlockHandler();

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'zoneId' => 3,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'some_param' => 'some_value',
                    ),
                    'viewType' => 'default',
                    'name' => 'My block',
                )
            ),
            $handler->moveBlock(1, 3)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::deleteBlock
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteBlock()
    {
        $handler = $this->createBlockHandler();

        $handler->deleteBlock(1);
        $handler->loadBlock(1);
    }
}
