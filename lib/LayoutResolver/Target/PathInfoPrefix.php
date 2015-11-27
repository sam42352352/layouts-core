<?php

namespace Netgen\BlockManager\LayoutResolver\Target;

use Netgen\BlockManager\LayoutResolver\Target;

class PathInfoPrefix extends Target
{
    /**
     * Returns the unique identifier of the target.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'path_info_prefix';
    }
}
