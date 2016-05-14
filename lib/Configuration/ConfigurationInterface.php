<?php

namespace Netgen\BlockManager\Configuration;

interface ConfigurationInterface
{
    const PARAMETER_NAMESPACE = 'netgen_block_manager';

    /**
     * Returns if parameter exists in configuration.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName);

    /**
     * Returns the parameter from configuration.
     *
     * @param string $parameterName
     *
     * @throws \InvalidArgumentException If parameter is undefined
     *
     * @return mixed
     */
    public function getParameter($parameterName);

    /**
     * Returns the configuration for specified block type.
     *
     * @param string $identifier
     *
     * @throws \InvalidArgumentException If configuration for specified block type does not exist
     *
     * @return array
     */
    public function getBlockTypeConfig($identifier);
}
