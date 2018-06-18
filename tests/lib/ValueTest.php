<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests;

use Netgen\BlockManager\Tests\Stubs\Value;
use PHPUnit\Framework\TestCase;

final class ValueTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Value::__construct
     */
    public function testSetProperties(): void
    {
        $value = new Value(
            [
                'someProperty' => 42,
                'someOtherProperty' => 84,
            ]
        );

        $this->assertSame(42, $value->someProperty);
        $this->assertSame(84, $value->someOtherProperty);
    }

    /**
     * @covers \Netgen\BlockManager\Value::__construct
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Property "someNonExistingProperty" does not exist in "Netgen\BlockManager\Tests\Stubs\Value" class.
     */
    public function testSetNonExistingProperties(): void
    {
        new Value(
            [
                'someNonExistingProperty' => 42,
            ]
        );
    }
}
