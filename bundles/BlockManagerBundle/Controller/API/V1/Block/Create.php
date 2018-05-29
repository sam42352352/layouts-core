<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\Block\BlockTypeException;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructBuilder;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Create extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructBuilder
     */
    private $createStructBuilder;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator
     */
    private $createStructValidator;

    public function __construct(
        BlockService $blockService,
        CreateStructBuilder $createStructBuilder,
        CreateStructValidator $createStructValidator
    ) {
        $this->blockService = $blockService;
        $this->createStructBuilder = $createStructBuilder;
        $this->createStructValidator = $createStructValidator;
    }

    /**
     * Creates the block in specified block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block type does not exist
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function __invoke(Block $block, Request $request)
    {
        $requestData = $request->attributes->get('data');

        $this->createStructValidator->validateCreateBlock($requestData);

        try {
            $blockType = $this->getBlockType($requestData->get('block_type'));
        } catch (BlockTypeException $e) {
            throw new BadStateException('block_type', 'Block type does not exist.', $e);
        }

        $blockCreateStruct = $this->createStructBuilder->buildCreateStruct($blockType);

        $createdBlock = $this->blockService->createBlock(
            $blockCreateStruct,
            $block,
            $requestData->get('placeholder'),
            $requestData->get('position')
        );

        return new View($createdBlock, Version::API_V1, Response::HTTP_CREATED);
    }
}
