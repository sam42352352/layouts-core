<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\Core\Values\Value;

class ItemCreateStruct extends Value
{
    /**
     * @var int|string
     */
    public $valueId;

    /**
     * @var string
     */
    public $valueType;

    /**
     * @var int
     */
    public $type;
}
