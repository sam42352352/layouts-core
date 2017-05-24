<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\LayoutService as LayoutServiceInterface;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct as APILayoutCopyStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct as APILayoutCreateStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct as APILayoutUpdateStruct;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Service\Mapper\LayoutMapper;
use Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\ZoneCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\ZoneUpdateStruct;

class LayoutService extends Service implements LayoutServiceInterface
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\LayoutValidator
     */
    protected $validator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    protected $mapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder
     */
    protected $structBuilder;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutHandler
     */
    protected $handler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutValidator $validator
     * @param \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper $mapper
     * @param \Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder $structBuilder
     */
    public function __construct(
        Handler $persistenceHandler,
        LayoutValidator $validator,
        LayoutMapper $mapper,
        LayoutStructBuilder $structBuilder
    ) {
        parent::__construct($persistenceHandler);

        $this->validator = $validator;
        $this->mapper = $mapper;
        $this->structBuilder = $structBuilder;

        $this->handler = $persistenceHandler->getLayoutHandler();
    }

    /**
     * Loads a layout with specified ID.
     *
     * @param int|string $layoutId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function loadLayout($layoutId)
    {
        $this->validator->validateId($layoutId, 'layoutId');

        return $this->mapper->mapLayout(
            $this->handler->loadLayout(
                $layoutId,
                Value::STATUS_PUBLISHED
            )
        );
    }

    /**
     * Loads a layout draft with specified ID.
     *
     * @param int|string $layoutId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function loadLayoutDraft($layoutId)
    {
        $this->validator->validateId($layoutId, 'layoutId');

        return $this->mapper->mapLayout(
            $this->handler->loadLayout(
                $layoutId,
                Value::STATUS_DRAFT
            )
        );
    }

    /**
     * Loads all layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     *
     * @param bool $includeDrafts
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout[]
     */
    public function loadLayouts($includeDrafts = false, $offset = 0, $limit = null)
    {
        $this->validator->validateOffsetAndLimit($offset, $limit);

        $persistenceLayouts = $this->handler->loadLayouts(
            $includeDrafts,
            $offset,
            $limit
        );

        $layouts = array();
        foreach ($persistenceLayouts as $persistenceLayout) {
            $layouts[] = $this->mapper->mapLayout($persistenceLayout);
        }

        return $layouts;
    }

    /**
     * Loads all shared layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     *
     * @param bool $includeDrafts
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout[]
     */
    public function loadSharedLayouts($includeDrafts = false, $offset = 0, $limit = null)
    {
        $this->validator->validateOffsetAndLimit($offset, $limit);

        $persistenceLayouts = $this->handler->loadSharedLayouts(
            $includeDrafts,
            $offset,
            $limit
        );

        $layouts = array();
        foreach ($persistenceLayouts as $persistenceLayout) {
            $layouts[] = $this->mapper->mapLayout($persistenceLayout);
        }

        return $layouts;
    }

    /**
     * Loads all layouts related to provided shared layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $sharedLayout
     * @param int $offset
     * @param int $limit
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided layout is not shared
     *                                                          If provided layout is not published
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout[]
     */
    public function loadRelatedLayouts(Layout $sharedLayout, $offset = 0, $limit = null)
    {
        if (!$sharedLayout->isPublished()) {
            throw new BadStateException('sharedLayout', 'Related layouts can only be loaded for published shared layouts.');
        }

        if (!$sharedLayout->isShared()) {
            throw new BadStateException('sharedLayout', 'Related layouts can only be loaded for shared layouts.');
        }

        $persistenceLayout = $this->handler->loadLayout($sharedLayout->getId(), $sharedLayout->getStatus());

        $relatedPersistenceLayouts = $this->handler->loadRelatedLayouts(
            $persistenceLayout,
            $offset,
            $limit
        );

        $relatedLayouts = array();
        foreach ($relatedPersistenceLayouts as $relatedPersistenceLayout) {
            $relatedLayouts[] = $this->mapper->mapLayout($relatedPersistenceLayout);
        }

        return $relatedLayouts;
    }

    /**
     * Loads the count of layouts related to provided shared layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $sharedLayout
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided layout is not shared
     *                                                          If provided layout is not published
     *
     * @return int
     */
    public function getRelatedLayoutsCount(Layout $sharedLayout)
    {
        if (!$sharedLayout->isPublished()) {
            throw new BadStateException('sharedLayout', 'Count of related layouts can only be loaded for published shared layouts.');
        }

        if (!$sharedLayout->isShared()) {
            throw new BadStateException('sharedLayout', 'Count of related layouts can only be loaded for shared layouts.');
        }

        $persistenceLayout = $this->handler->loadLayout($sharedLayout->getId(), $sharedLayout->getStatus());

        return $this->handler->getRelatedLayoutsCount($persistenceLayout);
    }

    /**
     * Returns if provided layout has a published status.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @return bool
     */
    public function hasPublishedState(Layout $layout)
    {
        return $this->handler->layoutExists($layout->getId(), Value::STATUS_PUBLISHED);
    }

    /**
     * Loads a zone with specified identifier.
     *
     * @param int|string $layoutId
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID or zone with specified identifier do not exist
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Zone
     */
    public function loadZone($layoutId, $identifier)
    {
        $this->validator->validateId($layoutId, 'layoutId');
        $this->validator->validateIdentifier($identifier, 'identifier', true);

        return $this->mapper->mapZone(
            $this->handler->loadZone(
                $layoutId,
                Value::STATUS_PUBLISHED,
                $identifier
            )
        );
    }

    /**
     * Loads a zone draft with specified identifier.
     *
     * @param int|string $layoutId
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID or zone with specified identifier do not exist
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Zone
     */
    public function loadZoneDraft($layoutId, $identifier)
    {
        $this->validator->validateId($layoutId, 'layoutId');
        $this->validator->validateIdentifier($identifier, 'identifier', true);

        return $this->mapper->mapZone(
            $this->handler->loadZone(
                $layoutId,
                Value::STATUS_DRAFT,
                $identifier
            )
        );
    }

    /**
     * Returns if layout with provided name exists.
     *
     * @param string $name
     * @param int|string $excludedLayoutId
     *
     * @return bool
     */
    public function layoutNameExists($name, $excludedLayoutId = null)
    {
        return $this->handler->layoutNameExists($name, $excludedLayoutId);
    }

    /**
     * Links the zone to provided linked zone. If zone had a previous link, it will be overwritten.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $linkedZone
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone is not a draft
     * @throws \Netgen\BlockManager\Exception\BadStateException If linked zone is not published
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone is in the shared layout
     * @throws \Netgen\BlockManager\Exception\BadStateException If linked zone is not in the shared layout
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone and linked zone belong to the same layout
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Zone
     */
    public function linkZone(Zone $zone, Zone $linkedZone)
    {
        if ($zone->isPublished()) {
            throw new BadStateException('zone', 'Only draft zones can be linked.');
        }

        if (!$linkedZone->isPublished()) {
            throw new BadStateException('linkedZone', 'Zones can only be linked to published zones.');
        }

        $persistenceLayout = $this->handler->loadLayout($zone->getLayoutId(), Value::STATUS_DRAFT);
        $persistenceZone = $this->handler->loadZone($zone->getLayoutId(), Value::STATUS_DRAFT, $zone->getIdentifier());

        $persistenceLinkedLayout = $this->handler->loadLayout($linkedZone->getLayoutId(), Value::STATUS_PUBLISHED);
        $persistenceLinkedZone = $this->handler->loadZone($linkedZone->getLayoutId(), Value::STATUS_PUBLISHED, $linkedZone->getIdentifier());

        if ($persistenceLayout->shared) {
            throw new BadStateException('zone', 'Zone cannot be in the shared layout.');
        }

        if ($persistenceZone->layoutId === $persistenceLinkedZone->layoutId) {
            throw new BadStateException('linkedZone', 'Linked zone needs to be in a different layout.');
        }

        if (!$persistenceLinkedLayout->shared) {
            throw new BadStateException('linkedZone', 'Linked zone is not in the shared layout.');
        }

        $updatedZone = $this->transaction(
            function () use ($persistenceZone, $persistenceLinkedZone) {
                return $this->handler->updateZone(
                    $persistenceZone,
                    new ZoneUpdateStruct(
                        array(
                            'linkedZone' => $persistenceLinkedZone,
                        )
                    )
                );
            }
        );

        return $this->mapper->mapZone($updatedZone);
    }

    /**
     * Removes the link in the zone.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Zone
     */
    public function unlinkZone(Zone $zone)
    {
        if ($zone->isPublished()) {
            throw new BadStateException('zone', 'Only draft zones can be unlinked.');
        }

        $persistenceZone = $this->handler->loadZone($zone->getLayoutId(), Value::STATUS_DRAFT, $zone->getIdentifier());

        $updatedZone = $this->transaction(
            function () use ($persistenceZone) {
                return $this->handler->updateZone(
                    $persistenceZone,
                    new ZoneUpdateStruct(
                        array(
                            'linkedZone' => false,
                        )
                    )
                );
            }
        );

        return $this->mapper->mapZone($updatedZone);
    }

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct $layoutCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function createLayout(APILayoutCreateStruct $layoutCreateStruct)
    {
        $this->validator->validateLayoutCreateStruct($layoutCreateStruct);

        if ($this->handler->layoutNameExists($layoutCreateStruct->name)) {
            throw new BadStateException('name', 'Layout with provided name already exists.');
        }

        $createdLayout = $this->transaction(
            function () use ($layoutCreateStruct) {
                $createdLayout = $this->handler->createLayout(
                    new LayoutCreateStruct(
                        array(
                            'type' => $layoutCreateStruct->layoutType->getIdentifier(),
                            'name' => $layoutCreateStruct->name,
                            'description' => $layoutCreateStruct->description,
                            'status' => Value::STATUS_DRAFT,
                            'shared' => $layoutCreateStruct->shared,
                        )
                    )
                );

                foreach ($layoutCreateStruct->layoutType->getZoneIdentifiers() as $zoneIdentifier) {
                    $this->handler->createZone(
                        $createdLayout,
                        new ZoneCreateStruct(
                            array(
                                'identifier' => $zoneIdentifier,
                            )
                        )
                    );
                }

                return $createdLayout;
            }
        );

        return $this->mapper->mapLayout($createdLayout);
    }

    /**
     * Updates a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct $layoutUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     *                                                          If layout with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function updateLayout(Layout $layout, APILayoutUpdateStruct $layoutUpdateStruct)
    {
        if ($layout->isPublished()) {
            throw new BadStateException('layout', 'Only draft layouts can be updated.');
        }

        $persistenceLayout = $this->handler->loadLayout($layout->getId(), Value::STATUS_DRAFT);

        $this->validator->validateLayoutUpdateStruct($layoutUpdateStruct);

        if ($layoutUpdateStruct->name !== null) {
            if ($this->handler->layoutNameExists($layoutUpdateStruct->name, $persistenceLayout->id)) {
                throw new BadStateException('name', 'Layout with provided name already exists.');
            }
        }

        $updatedLayout = $this->transaction(
            function () use ($persistenceLayout, $layoutUpdateStruct) {
                return $this->handler->updateLayout(
                    $persistenceLayout,
                    new LayoutUpdateStruct(
                        array(
                            'name' => $layoutUpdateStruct->name,
                            'description' => $layoutUpdateStruct->description,
                        )
                    )
                );
            }
        );

        return $this->mapper->mapLayout($updatedLayout);
    }

    /**
     * Copies a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct $layoutCopyStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function copyLayout(Layout $layout, APILayoutCopyStruct $layoutCopyStruct)
    {
        $this->validator->validateLayoutCopyStruct($layoutCopyStruct);

        if ($this->handler->layoutNameExists($layoutCopyStruct->name, $layout->getId())) {
            throw new BadStateException('layoutCopyStruct', 'Layout with provided name already exists.');
        }

        $persistenceLayout = $this->handler->loadLayout($layout->getId(), $layout->getStatus());

        $copiedLayout = $this->transaction(
            function () use ($persistenceLayout, $layoutCopyStruct) {
                return $this->handler->copyLayout(
                    $persistenceLayout,
                    new LayoutCopyStruct(
                        array(
                            'name' => $layoutCopyStruct->name,
                            'description' => $layoutCopyStruct->description,
                        )
                    )
                );
            }
        );

        return $this->mapper->mapLayout($copiedLayout);
    }

    /**
     * Changes the provided layout type.
     *
     * Zone mappings are multidimensional array where keys on the first level are
     * identifiers of the zones in the new layout type, while the values are the list
     * of old zones which will be mapped to the new one. i.e.
     *
     * array(
     *     'left' => array('left', 'right'),
     *     'top' => array('top'),
     * )
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\Layout\Type\LayoutType $targetLayoutType
     * @param array $zoneMappings
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     *                                                          If layout is already of provided target type
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function changeLayoutType(Layout $layout, LayoutType $targetLayoutType, array $zoneMappings = array())
    {
        if ($layout->isPublished()) {
            throw new BadStateException('layout', 'Layout type can only be changed for draft layouts.');
        }

        $persistenceLayout = $this->handler->loadLayout($layout->getId(), Value::STATUS_DRAFT);

        if ($persistenceLayout->type === $targetLayoutType->getIdentifier()) {
            throw new BadStateException('layout', 'Layout is already of provided target type.');
        }

        $this->validator->validateChangeLayoutType($layout, $targetLayoutType, $zoneMappings);

        $zoneMappings = array_merge(
            array_fill_keys($targetLayoutType->getZoneIdentifiers(), array()),
            $zoneMappings
        );

        $newLayout = $this->transaction(
            function () use ($persistenceLayout, $targetLayoutType, $zoneMappings) {
                return $this->handler->changeLayoutType(
                    $persistenceLayout,
                    $targetLayoutType->getIdentifier(),
                    $zoneMappings
                );
            }
        );

        return $this->mapper->mapLayout($newLayout);
    }

    /**
     * Creates a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param bool $discardExisting
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not published
     *                                                          If draft already exists for layout and $discardExisting is set to false
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function createDraft(Layout $layout, $discardExisting = false)
    {
        if (!$layout->isPublished()) {
            throw new BadStateException('layout', 'Drafts can only be created from published layouts.');
        }

        $persistenceLayout = $this->handler->loadLayout($layout->getId(), Value::STATUS_PUBLISHED);

        if ($this->handler->layoutExists($persistenceLayout->id, Value::STATUS_DRAFT)) {
            if (!$discardExisting) {
                throw new BadStateException('layout', 'The provided layout already has a draft.');
            }
        }

        $layoutDraft = $this->transaction(
            function () use ($persistenceLayout) {
                $this->handler->deleteLayout($persistenceLayout->id, Value::STATUS_DRAFT);
                $layoutDraft = $this->handler->createLayoutStatus($persistenceLayout, Value::STATUS_DRAFT);

                return $this->handler->updateLayout(
                    $layoutDraft,
                    new LayoutUpdateStruct(
                        array(
                            'modified' => time(),
                        )
                    )
                );
            }
        );

        return $this->mapper->mapLayout($layoutDraft);
    }

    /**
     * Discards a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     */
    public function discardDraft(Layout $layout)
    {
        if ($layout->isPublished()) {
            throw new BadStateException('layout', 'Only drafts can be discarded.');
        }

        $persistenceLayout = $this->handler->loadLayout($layout->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceLayout) {
                $this->handler->deleteLayout(
                    $persistenceLayout->id,
                    Value::STATUS_DRAFT
                );
            }
        );
    }

    /**
     * Publishes a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function publishLayout(Layout $layout)
    {
        if ($layout->isPublished()) {
            throw new BadStateException('layout', 'Only drafts can be published.');
        }

        $persistenceLayout = $this->handler->loadLayout($layout->getId(), Value::STATUS_DRAFT);

        $publishedLayout = $this->transaction(
            function () use ($persistenceLayout) {
                $this->handler->deleteLayout($persistenceLayout->id, Value::STATUS_ARCHIVED);

                if ($this->handler->layoutExists($persistenceLayout->id, Value::STATUS_PUBLISHED)) {
                    $archivedLayout = $this->handler->createLayoutStatus(
                        $this->handler->loadLayout($persistenceLayout->id, Value::STATUS_PUBLISHED),
                        Value::STATUS_ARCHIVED
                    );

                    // Update the archived layout to blank the name in order not to block
                    // usage of the old layout name.
                    // When restoring from archive, we need to reuse the name of the published
                    // layout.
                    $this->handler->updateLayout($archivedLayout, new LayoutUpdateStruct(array('name' => '')));

                    $this->handler->deleteLayout($persistenceLayout->id, Value::STATUS_PUBLISHED);
                }

                $publishedLayout = $this->handler->createLayoutStatus($persistenceLayout, Value::STATUS_PUBLISHED);
                $this->handler->deleteLayout($persistenceLayout->id, Value::STATUS_DRAFT);

                return $publishedLayout;
            }
        );

        return $this->mapper->mapLayout($publishedLayout);
    }

    /**
     * Deletes a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     */
    public function deleteLayout(Layout $layout)
    {
        $persistenceLayout = $this->handler->loadLayout($layout->getId(), $layout->getStatus());

        $this->transaction(
            function () use ($persistenceLayout) {
                $this->handler->deleteLayout(
                    $persistenceLayout->id
                );
            }
        );
    }

    /**
     * Creates a new layout create struct.
     *
     * @param \Netgen\BlockManager\Layout\Type\LayoutType $layoutType
     * @param string $name
     *
     * @return \Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct
     */
    public function newLayoutCreateStruct(LayoutType $layoutType, $name)
    {
        return $this->structBuilder->newLayoutCreateStruct($layoutType, $name);
    }

    /**
     * Creates a new layout update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @return \Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct
     */
    public function newLayoutUpdateStruct(Layout $layout = null)
    {
        return $this->structBuilder->newLayoutUpdateStruct($layout);
    }

    /**
     * Creates a new layout copy struct.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @return \Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct
     */
    public function newLayoutCopyStruct(Layout $layout = null)
    {
        return $this->structBuilder->newLayoutCopyStruct($layout);
    }
}
