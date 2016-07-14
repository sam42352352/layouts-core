<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Configuration;

use Netgen\BlockManager\Block\Form\ContentEditType;
use Netgen\BlockManager\Block\Form\DesignEditType;
use Netgen\BlockManager\Block\Form\FullEditType;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class BlockDefinitionsConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        $extension = new NetgenBlockManagerExtension();

        return new Configuration($extension->getAlias());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockDefinitionsNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testBlockDefinitionSettings()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'forms' => array(
                            'full' => array(
                                'type' => 'test_form',
                                'enabled' => true,
                            ),
                        ),
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                                'item_view_types' => array(
                                    'standard' => array(
                                        'name' => 'Standard',
                                    ),
                                ),
                            ),
                            'large' => array(
                                'name' => 'Large',
                                'item_view_types' => array(
                                    'standard' => array(
                                        'name' => 'Standard',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'forms' => array(
                        'full' => array(
                            'type' => 'test_form',
                            'enabled' => true,
                        ),
                        'design' => array(
                            'type' => DesignEditType::class,
                            'enabled' => false,
                            'parameters' => array(),
                        ),
                        'content' => array(
                            'type' => ContentEditType::class,
                            'enabled' => false,
                            'parameters' => array(),
                        ),
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                ),
                            ),
                        ),
                        'large' => array(
                            'name' => 'Large',
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockDefinitionsNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testBlockDefinitionSettingsWithDesignAndContentForms()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'forms' => array(
                            'full' => array(
                                'enabled' => false,
                            ),
                            'design' => array(
                                'type' => 'design_form',
                                'enabled' => true,
                                'parameters' => array('param1'),
                            ),
                            'content' => array(
                                'type' => 'content_form',
                                'enabled' => true,
                                'parameters' => array('param2'),
                            ),
                        ),
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                                'item_view_types' => array(
                                    'standard' => array(
                                        'name' => 'Standard',
                                    ),
                                ),
                            ),
                            'large' => array(
                                'name' => 'Large',
                                'item_view_types' => array(
                                    'standard' => array(
                                        'name' => 'Standard',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'forms' => array(
                        'full' => array(
                            'type' => FullEditType::class,
                            'enabled' => false,
                        ),
                        'design' => array(
                            'type' => 'design_form',
                            'enabled' => true,
                            'parameters' => array('param1'),
                        ),
                        'content' => array(
                            'type' => 'content_form',
                            'enabled' => true,
                            'parameters' => array('param2'),
                        ),
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                ),
                            ),
                        ),
                        'large' => array(
                            'name' => 'Large',
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockDefinitionsNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testBlockDefinitionSettingsNoViewTypesMerge()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                                'item_view_types' => array(
                                    'standard' => array(
                                        'name' => 'Standard',
                                    ),
                                ),
                            ),
                            'large' => array(
                                'name' => 'Large',
                                'item_view_types' => array(
                                    'standard' => array(
                                        'name' => 'Standard',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'block_definitions' => array(
                    'block' => array(
                        'view_types' => array(
                            'title' => array(
                                'name' => 'Title',
                                'item_view_types' => array(
                                    'standard' => array(
                                        'name' => 'Standard',
                                    ),
                                ),
                            ),
                            'image' => array(
                                'name' => 'Image',
                                'item_view_types' => array(
                                    'standard' => array(
                                        'name' => 'Standard',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'forms' => array(
                        'full' => array(
                            'type' => FullEditType::class,
                            'enabled' => true,
                        ),
                        'design' => array(
                            'type' => DesignEditType::class,
                            'enabled' => false,
                            'parameters' => array(),
                        ),
                        'content' => array(
                            'type' => ContentEditType::class,
                            'enabled' => false,
                            'parameters' => array(),
                        ),
                    ),
                    'view_types' => array(
                        'title' => array(
                            'name' => 'Title',
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                ),
                            ),
                        ),
                        'image' => array(
                            'name' => 'Image',
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockDefinitionsNodeDefinition
     */
    public function testBlockDefinitionSettingsWithNoBlockDefinitions()
    {
        $config = array(
            'block_definitions' => array(),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockDefinitionsNodeDefinition
     */
    public function testBlockDefinitionSettingsWithMissingContentForm()
    {
        $config = array(
            'block_definitions' => array(
                'block' => array(
                    'forms' => array(
                        'full' => array(
                            'enabled' => false,
                        ),
                        'design' => array(
                            'enabled' => true,
                        ),
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockDefinitionsNodeDefinition
     */
    public function testBlockDefinitionSettingsWithMissingDesignForm()
    {
        $config = array(
            'block_definitions' => array(
                'block' => array(
                    'forms' => array(
                        'full' => array(
                            'enabled' => false,
                        ),
                        'content' => array(
                            'enabled' => true,
                        ),
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockDefinitionsNodeDefinition
     */
    public function testBlockDefinitionSettingsWithFullAndDesignForm()
    {
        $config = array(
            'block_definitions' => array(
                'block' => array(
                    'forms' => array(
                        'full' => array(
                            'enabled' => true,
                        ),
                        'design' => array(
                            'enabled' => true,
                        ),
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockDefinitionsNodeDefinition
     */
    public function testBlockDefinitionSettingsWithFullAndContentForm()
    {
        $config = array(
            'block_definitions' => array(
                'block' => array(
                    'forms' => array(
                        'full' => array(
                            'enabled' => true,
                        ),
                        'content' => array(
                            'enabled' => true,
                        ),
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockDefinitionsNodeDefinition
     */
    public function testBlockDefinitionSettingsWithFullAndDesignAndContentForm()
    {
        $config = array(
            'block_definitions' => array(
                'block' => array(
                    'forms' => array(
                        'full' => array(
                            'enabled' => true,
                        ),
                        'design' => array(
                            'enabled' => true,
                        ),
                        'content' => array(
                            'enabled' => true,
                        ),
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockDefinitionsNodeDefinition
     */
    public function testBlockDefinitionSettingsWithNoViewTypes()
    {
        $config = array(
            'block_definitions' => array(
                'block' => array(
                    'view_types' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockDefinitionsNodeDefinition
     */
    public function testBlockDefinitionSettingsWithNoItemViewTypes()
    {
        $config = array(
            'block_definitions' => array(
                'block' => array(
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'item_view_types' => array(),
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }
}
