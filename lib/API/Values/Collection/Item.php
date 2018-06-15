<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Collection;

use DateTimeInterface;
use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Collection\Item\ItemDefinitionInterface;
use Netgen\BlockManager\Item\ItemInterface;

interface Item extends Value, ConfigAwareValue
{
    /**
     * Item of this type is inserted between items coming from the collection query.
     */
    public const TYPE_MANUAL = 0;

    /**
     * Items of this type override the item from the query at the specified position.
     */
    public const TYPE_OVERRIDE = 1;

    /**
     * Denotes that the item is visible. Does not take into account the possibility that
     * the CMS entity wrapped by the item might be hidden in CMS.
     */
    public const VISIBILITY_VISIBLE = 'visible';

    /**
     * Denotes that the item is hidden.
     */
    public const VISIBILITY_HIDDEN = 'hidden';

    /**
     * Denotes that the item is visible at certain time only, as configured by the scheduling
     * configuration.
     */
    public const VISIBILITY_SCHEDULED = 'scheduled';

    /**
     * Returns the item ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the ID of the collection to which the item belongs.
     *
     * @return int|string
     */
    public function getCollectionId();

    /**
     * Returns the item definition.
     */
    public function getDefinition(): ItemDefinitionInterface;

    /**
     * Returns the item position within the collection.
     */
    public function getPosition(): int;

    /**
     * Returns the type of this item.
     *
     * Type can either be manual (inserted between items returned from the query),
     * or override (replaces the item from the query in the same position).
     */
    public function getType(): int;

    /**
     * Returns the value stored inside the collection item.
     *
     * @return int|string
     */
    public function getValue();

    /**
     * Returns the CMS item loaded from value and value type stored in this collection item.
     */
    public function getCmsItem(): ItemInterface;

    /**
     * Returns if the item visibility is scheduled, as specified by item visibility/scheduling
     * configuration.
     */
    public function isScheduled(): bool;

    /**
     * Returns if the item is visible in provided point in time, as specified by item
     * visibility/scheduling configuration.
     *
     * If reference time is not provided, current time is used.
     */
    public function isVisible(DateTimeInterface $reference = null): bool;

    /**
     * Returns if the item is valid. An item is valid if it is visible (both the collection item
     * and CMS item) and if CMS item actually exists in the CMS.
     */
    public function isValid(): bool;
}
