<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Serializer\Normalizer\LayoutViewNormalizer;
use Netgen\BlockManager\View\LayoutView;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use DateTime;

class LayoutViewNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\LayoutViewNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\LayoutViewNormalizer::normalize
     * @covers \Netgen\BlockManager\Serializer\Normalizer\LayoutViewNormalizer::getZones
     * @covers \Netgen\BlockManager\Serializer\Normalizer\LayoutViewNormalizer::getBlocks
     * @covers \Netgen\BlockManager\Serializer\Normalizer\LayoutViewNormalizer::getBlockPositions
     */
    public function testNormalize()
    {
        $currentDate = new DateTime();
        $currentDate->setTimestamp(time());

        $layout = new Layout(
            array(
                'id' => 42,
                'parentId' => null,
                'identifier' => '3_zones_a',
                'created' => $currentDate,
                'modified' => $currentDate,
                'zones' => array(
                    new Zone(
                        array(
                            'id' => 84,
                            'layoutId' => 42,
                            'identifier' => 'left',
                        )
                    ),
                    new Zone(
                        array(
                            'id' => 85,
                            'layoutId' => 42,
                            'identifier' => 'right',
                        )
                    ),
                ),
            )
        );

        $block = new Block(
            array(
                'id' => 24,
            )
        );

        $normalizedBlock = array(
            'id' => 24,
        );

        $layoutView = new LayoutView();
        $layoutView->setLayout($layout);
        $layoutView->addParameters(
            array(
                'blocks' => array(
                    'left' => array($block),
                ),
            )
        );

        $config = array(
            'name' => '3 zones A',
            'zones' => array(
                'left' => array(),
                'right' => array(
                    'allowed_blocks' => array('paragraph'),
                ),
            ),
        );

        $configuration = $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface');
        $configuration
            ->expects($this->any())
            ->method('getLayoutConfig')
            ->with($this->equalTo('3_zones_a'))
            ->will($this->returnValue($config));

        $blockNormalizerMock = $this
            ->getMockBuilder('Netgen\BlockManager\Serializer\Normalizer\BlockNormalizer')
            ->disableOriginalConstructor()
            ->getMock();

        $blockNormalizerMock
            ->expects($this->once())
            ->method('normalize')
            ->with($this->equalTo($block))
            ->will($this->returnValue($normalizedBlock));

        $viewRendererMock = $this->getMock('Netgen\BlockManager\View\ViewRendererInterface');
        $viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with($this->equalTo($layoutView))
            ->will($this->returnValue('rendered layout view'));

        $layoutViewNormalizer = new LayoutViewNormalizer($configuration, $blockNormalizerMock, $viewRendererMock);

        self::assertEquals(
            array(
                'id' => $layout->getId(),
                'parent_id' => $layout->getParentId(),
                'identifier' => $layout->getIdentifier(),
                'created_at' => $layout->getCreated(),
                'updated_at' => $layout->getModified(),
                'name' => $layout->getName(),
                'html' => 'rendered layout view',
                'zones' => array(
                    array(
                        'identifier' => 'left',
                        'allowed_blocks' => true,
                    ),
                    array(
                        'identifier' => 'right',
                        'allowed_blocks' => array('paragraph'),
                    ),
                ),
                'blocks' => array($normalizedBlock),
                'positions' => array(
                    array(
                        'zone' => 'left',
                        'blocks' => array(
                            array(
                                'block_id' => $block->getId(),
                            ),
                        ),
                    ),
                    array(
                        'zone' => 'right',
                        'blocks' => array(),
                    ),
                ),
            ),
            $layoutViewNormalizer->normalize($layoutView)
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\LayoutViewNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, $expected)
    {
        $configuration = $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface');

        $blockNormalizerMock = $this
            ->getMockBuilder('Netgen\BlockManager\Serializer\Normalizer\BlockNormalizer')
            ->disableOriginalConstructor()
            ->getMock();

        $viewRendererMock = $this->getMock('Netgen\BlockManager\View\ViewRendererInterface');

        $layoutViewNormalizer = new LayoutViewNormalizer($configuration, $blockNormalizerMock, $viewRendererMock);

        self::assertEquals($expected, $layoutViewNormalizer->supportsNormalization($data));
    }

    /**
     * Provider for {@link self::testSupportsNormalization}.
     *
     * @return array
     */
    public function supportsNormalizationProvider()
    {
        return array(
            array(null, false),
            array(true, false),
            array(false, false),
            array('layout_view', false),
            array(array(), false),
            array(42, false),
            array(42.12, false),
            array(new Value(), false),
            array(new LayoutView(), true),
        );
    }
}
