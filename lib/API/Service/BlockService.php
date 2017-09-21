<?php

namespace Netgen\BlockManager\API\Service;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Block\BlockDefinitionInterface;

interface BlockService extends Service
{
    /**
     * Loads a block with specified ID.
     *
     * If $locale is a string, the block is loaded in specified locale.
     * If $locale is an array of strings, the first available locale will
     * be returned.
     *
     * If the block is always available and $useMainLocale is set to true,
     * block in main locale will be returned if none of the locales in $locale
     * array are found.
     *
     * @param int|string $blockId
     * @param string|string[] $locale
     * @param bool $useMainLocale
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function loadBlock($blockId, $locale = null, $useMainLocale = true);

    /**
     * Loads a block draft with specified ID.
     *
     * If $locale is a string, the block is loaded in specified locale.
     * If $locale is an array of strings, the first available locale will
     * be returned.
     *
     * If the block is always available and $useMainLocale is set to true,
     * block in main locale will be returned if none of the locales in $locale
     * array are found.
     *
     * @param int|string $blockId
     * @param string|string[] $locale
     * @param bool $useMainLocale
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function loadBlockDraft($blockId, $locale = null, $useMainLocale = true);

    /**
     * Loads all blocks belonging to provided zone.
     *
     * If $locale is a string, the block is loaded in specified locale.
     * If $locale is an array of strings, the first available locale will
     * be returned.
     *
     * If the block is always available and $useMainLocale is set to true,
     * block in main locale will be returned if none of the locales in $locale
     * array are found.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param string|string[] $locale
     * @param bool $useMainLocale
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block[]
     */
    public function loadZoneBlocks(Zone $zone, $locale = null, $useMainLocale = true);

    /**
     * Loads all blocks belonging to provided layout.
     *
     * If $locale is a string, the block is loaded in specified locale.
     * If $locale is an array of strings, the first available locale will
     * be returned.
     *
     * If the block is always available and $useMainLocale is set to true,
     * block in main locale will be returned if none of the locales in $locale
     * array are found.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param string|string[] $locale
     * @param bool $useMainLocale
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block[]
     */
    public function loadLayoutBlocks(Layout $layout, $locale = null, $useMainLocale = true);

    /**
     * Returns if provided block has a published status.
     *
     * If $locales is an array, returned blocks will only have specified translations.
     * If $locales is true, returned blocks will have all translations, otherwise, the main
     * translation will be returned.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool
     */
    public function hasPublishedState(Block $block);

    /**
     * Creates a block in specified block and placeholder and at specified position.
     *
     * If position is not provided, bock is placed at the end of the placeholder.
     *
     * @param \Netgen\BlockManager\API\Values\Block\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\API\Values\Block\Block $targetBlock
     * @param string $placeholder
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If target block is not a draft
     *                                                          If target block is not a container
     *                                                          If placeholder does not exist in the target block
     *                                                          If new block is a container
     *                                                          If provided position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function createBlock(BlockCreateStruct $blockCreateStruct, Block $targetBlock, $placeholder, $position = null);

    /**
     * Creates a block in specified zone and at specified position.
     *
     * If position is not provided, block is placed at the end of the zone.
     *
     * @param \Netgen\BlockManager\API\Values\Block\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone is not a draft
     *                                                          If provided position is out of range
     *                                                          If block cannot be placed in specified zone
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function createBlockInZone(BlockCreateStruct $blockCreateStruct, Zone $zone, $position = null);

    /**
     * Updates a specified block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Block\BlockUpdateStruct $blockUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     *                                                          If block does not have a specified translation
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function updateBlock(Block $block, BlockUpdateStruct $blockUpdateStruct);

    /**
     * Copies a block to a specified target block and placeholder.
     *
     * The block is placed at the end of placeholder.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Block\Block $targetBlock
     * @param string $placeholder
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If source or target block is not a draft
     *                                                          If target block is not a container
     *                                                          If target block is in a different layout
     *                                                          If placeholder does not exist in the target block
     *                                                          If new block is a container
     *                                                          If target block is within the provided block
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function copyBlock(Block $block, Block $targetBlock, $placeholder);

    /**
     * Copies a block to a specified zone.
     *
     * Block is placed at the end of the zone.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block or zone are not drafts
     *                                                          If zone is in a different layout
     *                                                          If block cannot be placed in specified zone
     *                                                              as specified by the list of blocks allowed within the zone
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function copyBlockToZone(Block $block, Zone $zone);

    /**
     * Moves a block to specified target block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Block\Block $targetBlock
     * @param string $placeholder
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If source or target block is not a draft
     *                                                          If target block is not a container
     *                                                          If target block is in a different layout
     *                                                          If placeholder does not exist in the target block
     *                                                          If new block is a container
     *                                                          If target block is within the provided block
     *                                                          If provided position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function moveBlock(Block $block, Block $targetBlock, $placeholder, $position);

    /**
     * Moves a block to specified position inside the zone.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block or zone are not drafts
     *                                                          If zone is in a different layout
     *                                                          If provided position is out of range
     *                                                          If block cannot be placed in specified zone
     *                                                              as specified by the list of blocks allowed within the zone
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function moveBlockToZone(Block $block, Zone $zone, $position);

    /**
     * Restores the specified block from the published status.
     *
     * Placement and position of the block are kept as is.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function restoreBlock(Block $block);

    /**
     * Enables translating the block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function enableTranslations(Block $block);

    /**
     * Disable translating the block. All translations (except the main one) will be removed.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function disableTranslations(Block $block);

    /**
     * Deletes a specified block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     */
    public function deleteBlock(Block $block);

    /**
     * Creates a new block create struct from data found in provided block definition.
     *
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition
     *
     * @return \Netgen\BlockManager\API\Values\Block\BlockCreateStruct
     */
    public function newBlockCreateStruct(BlockDefinitionInterface $blockDefinition);

    /**
     * Creates a new block update struct in specified locale.
     *
     * If block is provided, initial data is copied from the block.
     *
     * @param string $locale
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\API\Values\Block\BlockUpdateStruct
     */
    public function newBlockUpdateStruct($locale, Block $block = null);
}
