<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\Parameter;

class TextHandler extends BlockDefinitionHandler
{
    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'content' => new Parameter\Text(array(), true),
        ) + $this->getCommonParameters();
    }
}
