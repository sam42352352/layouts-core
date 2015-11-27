<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler;
use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Normalizer;
use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler;
use Netgen\BlockManager\Tests\DoctrineDatabaseTrait;

class HandlerTest extends \PHPUnit_Framework_TestCase
{
    use DoctrineDatabaseTrait;

    /**
     * Sets up the database connection.
     */
    protected function setUp()
    {
        $this->prepareDatabase(__DIR__ . '/_fixtures/schema', __DIR__ . '/_fixtures');
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler::addTargetHandler
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler::loadRules
     */
    public function testLoadRules()
    {
        $handler = $this->createHandler();

        $expected = array(
            1 => array(
                'layout_id' => 1,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $handler->loadRules('route', array('my_cool_route')));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler::addTargetHandler
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler::loadRules
     */
    public function testLoadRulesWithCondition()
    {
        $handler = $this->createHandler();

        $expected = array(
            2 => array(
                'layout_id' => 2,
                'conditions' => array(
                    1 => array(
                        'identifier' => 'route_parameter',
                        'parameters' => array('some_param' => array('1', '2')),
                    ),
                ),
            ),
        );

        self::assertEquals($expected, $handler->loadRules('route', array('my_second_cool_route')));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler::loadRules
     */
    public function testLoadRulesWithMultipleConditions()
    {
        $handler = $this->createHandler();

        $expected = array(
            3 => array(
                'layout_id' => 3,
                'conditions' => array(
                    2 => array(
                        'identifier' => 'route_parameter',
                        'parameters' => array('some_param' => array('3', '4')),
                    ),
                    3 => array(
                        'identifier' => 'route_parameter',
                        'parameters' => array('some_other_param' => array('5', '6')),
                    ),
                ),
            ),
        );

        self::assertEquals($expected, $handler->loadRules('route', array('my_fourth_cool_route')));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler::loadRules
     */
    public function testLoadRulesWithNoRules()
    {
        $handler = $this->createHandler();
        self::assertEquals(array(), $handler->loadRules('route', array('some_non_existing_route')));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler::loadRules
     * @expectedException \InvalidArgumentException
     */
    public function testLoadRulesWithNonExistingTargetHandler()
    {
        $handler = $this->createHandler();
        $handler->loadRules('non_existing_target', array(1, 2, 3));
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\RuleHandlerInterface
     */
    protected function createHandler()
    {
        $handler = new Handler($this->databaseConnection, new Normalizer());
        $handler->addTargetHandler(new TargetHandler\Route());

        return $handler;
    }
}
