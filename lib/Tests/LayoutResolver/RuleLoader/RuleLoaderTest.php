<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleLoader;

use Netgen\BlockManager\LayoutResolver\Rule;
use Netgen\BlockManager\LayoutResolver\RuleBuilder\RuleBuilderInterface;
use Netgen\BlockManager\LayoutResolver\RuleHandler\RuleHandlerInterface;;
use Netgen\BlockManager\LayoutResolver\RuleLoader\RuleLoader;
use Netgen\BlockManager\Tests\LayoutResolver\Stubs\Target;

class RuleLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleHandlerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleBuilderMock;

    public function setUp()
    {
        $this->ruleHandlerMock = $this->getMock(
            RuleHandlerInterface::class
        );

        $this->ruleBuilderMock = $this->getMock(
            RuleBuilderInterface::class
        );
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleLoader\RuleLoader::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\RuleLoader\RuleLoader::loadRules
     */
    public function testLoadRules()
    {
        $target = new Target(array(42));
        $rule = new Rule(42, $target);

        $this->ruleHandlerMock
            ->expects($this->once())
            ->method('loadRules')
            ->with($this->equalTo($target->getIdentifier()), $this->equalTo($target->getValues()))
            ->will($this->returnValue(array('some_data')));

        $this->ruleBuilderMock
            ->expects($this->once())
            ->method('buildRules')
            ->with($this->equalTo($target), $this->equalTo(array('some_data')))
            ->will($this->returnValue(array($rule)));

        $ruleLoader = $this->getRuleLoader();
        self::assertEquals(array($rule), $ruleLoader->loadRules($target));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleLoader\RuleLoader::loadRules
     */
    public function testLoadRulesWithEmptyValues()
    {
        $target = new Target(array());

        $this->ruleHandlerMock
            ->expects($this->never())
            ->method('loadRules');

        $this->ruleBuilderMock
            ->expects($this->never())
            ->method('buildRules');

        $ruleLoader = $this->getRuleLoader();
        self::assertEquals(array(), $ruleLoader->loadRules($target));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleLoader\RuleLoader::loadRules
     */
    public function testLoadRulesWithNoDataFound()
    {
        $target = new Target(array(42));

        $this->ruleHandlerMock
            ->expects($this->once())
            ->method('loadRules')
            ->with($this->equalTo($target->getIdentifier()), $this->equalTo($target->getValues()))
            ->will($this->returnValue(array()));

        $this->ruleBuilderMock
            ->expects($this->never())
            ->method('buildRules');

        $ruleLoader = $this->getRuleLoader();
        self::assertEquals(array(), $ruleLoader->loadRules($target));
    }

    /**
     * Returns the rule loader under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleLoader\RuleLoaderInterface
     */
    protected function getRuleLoader()
    {
        return new RuleLoader(
            $this->ruleHandlerMock,
            $this->ruleBuilderMock
        );
    }
}
