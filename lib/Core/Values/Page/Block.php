<?php

namespace Netgen\BlockManager\Core\Values\Page;

use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
use Netgen\BlockManager\Core\Values\ParameterBasedValueTrait;
use Netgen\BlockManager\ValueObject;

class Block extends ValueObject implements APIBlock
{
    use ParameterBasedValueTrait;

    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int|string
     */
    protected $layoutId;

    /**
     * @var string
     */
    protected $zoneIdentifier;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected $definition;

    /**
     * @var bool
     */
    protected $published;

    /**
     * @var string
     */
    protected $viewType;

    /**
     * @var string
     */
    protected $itemViewType;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $status;

    /**
     * Returns the block ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns layout ID to which this block belongs.
     *
     * @return int|string
     */
    public function getLayoutId()
    {
        return $this->layoutId;
    }

    /**
     * Returns zone identifier to which this block belongs.
     *
     * @return string
     */
    public function getZoneIdentifier()
    {
        return $this->zoneIdentifier;
    }

    /**
     * Returns the position of this block in the zone.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Returns the block definition.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Returns if the block is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Returns view type which will be used to render this block.
     *
     * @return string
     */
    public function getViewType()
    {
        return $this->viewType;
    }

    /**
     * Returns item view type which will be used to render block items.
     *
     * @return string
     */
    public function getItemViewType()
    {
        return $this->itemViewType;
    }

    /**
     * Returns the human readable name of the block.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the status of the block.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
}
