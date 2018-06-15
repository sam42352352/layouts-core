<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters;

use Netgen\BlockManager\Value;

final class Parameter extends Value
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterDefinition
     */
    protected $parameterDefinition;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var bool
     */
    protected $isEmpty = true;

    /**
     * Returns the string representation of the parameter value.
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Returns the parameter name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the parameter definition.
     */
    public function getParameterDefinition(): ParameterDefinition
    {
        return $this->parameterDefinition;
    }

    /**
     * Returns the parameter value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns if the parameter value is empty.
     */
    public function isEmpty(): bool
    {
        return $this->isEmpty;
    }
}
