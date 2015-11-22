<?php

namespace Netgen\BlockManager\BlockDefinition\Definition;

use Netgen\BlockManager\BlockDefinition\BlockDefinition;
use Netgen\BlockManager\BlockDefinition\Parameters;
use Netgen\BlockManager\API\Values\Page\Block;
use Symfony\Component\Validator\Constraints;

class Title extends BlockDefinition
{
    /**
     * @var array
     */
    protected $options = array(
        'h1' => 'h1',
        'h2' => 'h2',
        'h3' => 'h3',
    );

    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'title';
    }

    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\BlockDefinition\Parameter[]
     */
    public function getParameters()
    {
        return array(
            'tag' => new Parameters\Select(
                'h2',
                array('options' => $this->options)
            ),
            'title' => new Parameters\Text('Title'),
        ) + parent::getParameters();
    }

    /**
     * Returns the array specifying block parameter human readable names.
     *
     * @return string[]
     */
    public function getParameterNames()
    {
        return array(
            'tag' => 'Tag',
            'title' => 'Title',
        ) + parent::getParameterNames();
    }

    /**
     * Returns the array specifying block parameter validator constraints.
     *
     * @return array
     */
    public function getParameterConstraints()
    {
        return array(
            'tag' => array(
                new Constraints\NotBlank(),
                new Constraints\Choice(array('choices' => $this->options)),
            ),
            'title' => array(
                new Constraints\NotBlank(),
            ),
        ) + parent::getParameterConstraints();
    }

    /**
     * Returns the array of values provided by this block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return array
     */
    public function getValues(Block $block)
    {
        return array();
    }
}
