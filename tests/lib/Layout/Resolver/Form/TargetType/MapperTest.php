<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\TargetType;

use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper;
use PHPUnit\Framework\TestCase;

class MapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = $this->getMockForAbstractClass(Mapper::class);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::getFormOptions
     */
    public function testGetFormOptions()
    {
        $this->assertEquals(
            array(),
            $this->mapper->getFormOptions()
        );
    }
}
