<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Collection;

use Netgen\BlockManager\Exception\Collection\ItemDefinitionException;
use PHPUnit\Framework\TestCase;

final class ItemDefinitionExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Collection\ItemDefinitionException::noItemDefinition
     */
    public function testNoItemDefinition(): void
    {
        $exception = ItemDefinitionException::noItemDefinition('type');

        $this->assertSame(
            'Item definition for "type" value type does not exist.',
            $exception->getMessage()
        );
    }
}
