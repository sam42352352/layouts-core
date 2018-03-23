<?php

namespace Netgen\BlockManager\API\Values\Layout;

use Netgen\BlockManager\Value;

final class LayoutUpdateStruct extends Value
{
    /**
     * New human readable name of the layout.
     *
     * @var string
     */
    public $name;

    /**
     * New description of the layout.
     *
     * @var string
     */
    public $description;
}
