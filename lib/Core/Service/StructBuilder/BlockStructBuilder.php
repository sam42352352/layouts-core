<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Service\StructBuilder;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Block\BlockDefinitionInterface;

final class BlockStructBuilder
{
    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\ConfigStructBuilder
     */
    private $configStructBuilder;

    public function __construct(ConfigStructBuilder $configStructBuilder)
    {
        $this->configStructBuilder = $configStructBuilder;
    }

    /**
     * Creates a new block create struct from data found in provided block definition.
     */
    public function newBlockCreateStruct(BlockDefinitionInterface $blockDefinition): BlockCreateStruct
    {
        $viewTypeIdentifier = $blockDefinition->getViewTypeIdentifiers()[0];
        $viewType = $blockDefinition->getViewType($viewTypeIdentifier);
        $itemViewTypeIdentifier = $viewType->getItemViewTypeIdentifiers()[0];

        $blockCreateStruct = new BlockCreateStruct(
            [
                'definition' => $blockDefinition,
                'viewType' => $viewTypeIdentifier,
                'itemViewType' => $itemViewTypeIdentifier,
                'isTranslatable' => $blockDefinition->isTranslatable(),
                'alwaysAvailable' => true,
            ]
        );

        $blockCreateStruct->fillParameters($blockDefinition);

        return $blockCreateStruct;
    }

    /**
     * Creates a new block update struct in specified locale.
     *
     * If block is provided, initial data is copied from the block.
     */
    public function newBlockUpdateStruct(string $locale, Block $block = null): BlockUpdateStruct
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        $blockUpdateStruct->locale = $locale;

        if (!$block instanceof Block) {
            return $blockUpdateStruct;
        }

        $blockUpdateStruct->viewType = $block->getViewType();
        $blockUpdateStruct->itemViewType = $block->getItemViewType();
        $blockUpdateStruct->name = $block->getName();
        $blockUpdateStruct->alwaysAvailable = $block->isAlwaysAvailable();
        $blockUpdateStruct->fillParametersFromBlock($block);

        $this->configStructBuilder->buildConfigUpdateStructs($block, $blockUpdateStruct);

        return $blockUpdateStruct;
    }
}
