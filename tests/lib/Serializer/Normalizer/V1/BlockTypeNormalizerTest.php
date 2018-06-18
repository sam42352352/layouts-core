<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Serializer\Normalizer\V1\BlockTypeNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinitionHandler;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

final class BlockTypeNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\BlockTypeNormalizer
     */
    private $normalizer;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    private $blockDefinition;

    public function setUp(): void
    {
        $this->blockDefinition = new BlockDefinition(['identifier' => 'title']);

        $this->normalizer = new BlockTypeNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockTypeNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $blockType = new BlockType(
            [
                'identifier' => 'identifier',
                'icon' => '/icon.svg',
                'isEnabled' => false,
                'name' => 'Block type',
                'definition' => $this->blockDefinition,
                'defaults' => [
                    'name' => 'Default name',
                    'view_type' => 'Default view type',
                    'parameters' => ['param' => 'value'],
                ],
            ]
        );

        $this->assertSame(
            [
                'identifier' => $blockType->getIdentifier(),
                'enabled' => false,
                'name' => $blockType->getName(),
                'icon' => $blockType->getIcon(),
                'definition_identifier' => $this->blockDefinition->getIdentifier(),
                'is_container' => false,
                'defaults' => $blockType->getDefaults(),
            ],
            $this->normalizer->normalize(new VersionedValue($blockType, 1))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockTypeNormalizer::normalize
     */
    public function testNormalizeWithContainerBlock(): void
    {
        $blockType = new BlockType(
            [
                'identifier' => 'definition',
                'name' => 'Block type',
                'isEnabled' => true,
                'definition' => new ContainerDefinition(
                    [
                        'identifier' => 'definition',
                        'handler' => new ContainerDefinitionHandler(),
                    ]
                ),
            ]
        );

        $data = $this->normalizer->normalize(new VersionedValue($blockType, 1));

        $this->assertTrue($data['is_container']);
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockTypeNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, bool $expected): void
    {
        $this->assertSame($expected, $this->normalizer->supportsNormalization($data));
    }

    public function supportsNormalizationProvider(): array
    {
        return [
            [null, false],
            [true, false],
            [false, false],
            ['block', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new Value(), false],
            [new BlockType(), false],
            [new VersionedValue(new Value(), 1), false],
            [new VersionedValue(new BlockType(), 2), false],
            [new VersionedValue(new BlockType(), 1), true],
        ];
    }
}
