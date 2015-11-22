<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleBuilder;

use Netgen\BlockManager\LayoutResolver\RuleBuilder\RuleBuilder;
use Netgen\BlockManager\LayoutResolver\Condition;
use Netgen\BlockManager\LayoutResolver\Rule;

class RuleBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleBuilder\RuleBuilder::buildRules
     */
    public function testBuildRules()
    {
        $data = array(
            array(
                'layout_id' => 42,
                'conditions' => array(
                    array(
                        'identifier' => 'condition',
                        'value_identifier' => 'identifier',
                        'values' => array(1, 2, 3),
                    ),
                ),
            ),
            array(
                'layout_id' => 84,
                'conditions' => array(),
            ),
        );

        $rule1 = new Rule(
            42,
            array(
                new Condition(
                    'condition',
                    'identifier',
                    array(1, 2, 3)
                ),
            )
        );

        $rule2 = new Rule(84);

        $rules = array($rule1, $rule2);

        $ruleBuilder = new RuleBuilder();
        self::assertEquals($rules, $ruleBuilder->buildRules($data));
    }
}
