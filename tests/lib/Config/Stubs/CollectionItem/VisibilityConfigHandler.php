<?php

namespace Netgen\BlockManager\Tests\Config\Stubs\CollectionItem;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;

final class VisibilityConfigHandler implements ConfigDefinitionHandlerInterface
{
    public function getParameterDefinitions()
    {
        return array(
            'visibility_status' => new ParameterDefinition(
                array(
                    'name' => 'visibility_status',
                    'type' => new ParameterType\ChoiceType(),
                    'options' => array(
                        'multiple' => false,
                        'options' => array(
                            Item::VISIBILITY_VISIBLE => Item::VISIBILITY_VISIBLE,
                            Item::VISIBILITY_HIDDEN => Item::VISIBILITY_HIDDEN,
                            Item::VISIBILITY_SCHEDULED => Item::VISIBILITY_SCHEDULED,
                        ),
                    ),
                )
            ),
            'visible_from' => new ParameterDefinition(
                array(
                    'name' => 'visible_from',
                    'type' => new ParameterType\DateTimeType(),
                )
            ),
            'visible_to' => new ParameterDefinition(
                array(
                    'name' => 'visible_to',
                    'type' => new ParameterType\DateTimeType(),
                )
            ),
        );
    }

    public function buildParameters(ParameterBuilderInterface $builder)
    {
    }

    public function isEnabled(ConfigAwareValue $configAwareValue)
    {
        return true;
    }
}
