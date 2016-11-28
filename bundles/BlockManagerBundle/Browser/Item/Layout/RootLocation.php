<?php

namespace Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout;

use Netgen\ContentBrowser\Item\LocationInterface;

class RootLocation implements LocationInterface
{
    /**
     * Returns the location ID.
     *
     * @return int|string
     */
    public function getLocationId()
    {
        return 0;
    }

    /**
     * Returns the type.
     *
     * @return int|string
     */
    public function getType()
    {
        return Item::TYPE;
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return 'All layouts';
    }

    /**
     * Returns the parent ID.
     *
     * @return int|string
     */
    public function getParentId()
    {
        return null;
    }
}
