<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\Handler\Plugin;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

final class HandlerPlugin extends Plugin
{
    /**
     * @var string[]
     */
    private static $extendedHandlers = array();

    public static function instance(array $extendedHandlers = array())
    {
        self::$extendedHandlers = $extendedHandlers;

        return new self();
    }

    public static function getExtendedHandler()
    {
        return self::$extendedHandlers;
    }

    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add('test_param', ParameterType\TextLineType::class);
    }

    public function getDynamicParameters(DynamicParameters $params, Block $block)
    {
        $params['dynamic_param'] = 'dynamic_value';
    }
}
