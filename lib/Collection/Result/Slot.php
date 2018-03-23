<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\Value;

/**
 * The slot represents a placeholder for an item when executing
 * a context dependant query when there's no context.
 */
final class Slot extends Value implements ItemInterface
{
    public function getValue()
    {
        return 0;
    }

    public function getRemoteId()
    {
        return 0;
    }

    public function getValueType()
    {
        return 'slot';
    }

    public function getName()
    {
        return '(UNKNOWN ITEM)';
    }

    public function isVisible()
    {
        return true;
    }

    public function getObject()
    {
        return null;
    }
}
