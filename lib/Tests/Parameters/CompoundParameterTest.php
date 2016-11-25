<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Parameters\ParameterType\Compound\BooleanType;
use Netgen\BlockManager\Parameters\CompoundParameter;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterCollection;
use PHPUnit\Framework\TestCase;

class CompoundParameterTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::__construct
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::getParameter
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::getParameters
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::hasParameter
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::buildParameters
     */
    public function testDefaultProperties()
    {
        $parameter = new CompoundParameter('name', new BooleanType());

        $this->assertEquals(array(), $parameter->getParameters());
        $this->assertFalse($parameter->hasParameter('test'));

        try {
            $this->assertEquals(array(), $parameter->getParameter('test'));
            $this->fail('Fetched a parameter in empty collection.');
        } catch (InvalidArgumentException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::getParameter
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::getParameters
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::hasParameter
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::buildParameters
     */
    public function testSetProperties()
    {
        $parameter = new CompoundParameter(
            'name',
            new BooleanType(),
            array(
                'required' => true,
                'default_value' => 42,
                'groups' => array('group'),
            ),
            array(
                'name' => 'value',
            )
        );

        $this->assertEquals(array('name' => 'value'), $parameter->getParameters());

        $this->assertFalse($parameter->hasParameter('test'));
        $this->assertTrue($parameter->hasParameter('name'));

        try {
            $this->assertEquals(array(), $parameter->getParameter('test'));
            $this->fail('Fetched a parameter in empty collection.');
        } catch (InvalidArgumentException $e) {
            // Do nothing
        }

        $this->assertEquals('value', $parameter->getParameter('name'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::getParameter
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::getParameters
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::hasParameter
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::buildParameters
     */
    public function testSetPropertiesWithClosure()
    {
        $parameter = new ParameterCollection(
            function () {
                return array(
                    'name' => 'value',
                );
            }
        );

        $this->assertEquals(array('name' => 'value'), $parameter->getParameters());

        $this->assertFalse($parameter->hasParameter('test'));
        $this->assertTrue($parameter->hasParameter('name'));

        try {
            $this->assertEquals(array(), $parameter->getParameter('test'));
            $this->fail('Fetched a parameter in empty collection.');
        } catch (InvalidArgumentException $e) {
            // Do nothing
        }

        $this->assertEquals('value', $parameter->getParameter('name'));
    }
}
