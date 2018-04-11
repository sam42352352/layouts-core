<?php

namespace Netgen\BlockManager\Tests\Config\Stubs\Block;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;

final class HttpCacheConfigHandler implements ConfigDefinitionHandlerInterface
{
    public function getParameterDefinitions()
    {
        return array(
            'use_http_cache' => new ParameterDefinition(
                array(
                    'name' => 'use_http_cache',
                    'type' => new ParameterType\BooleanType(),
                )
            ),
            'shared_max_age' => new ParameterDefinition(
                array(
                    'name' => 'shared_max_age',
                    'type' => new ParameterType\IntegerType(),
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
