<?php

namespace Netgen\BlockManager\Collection\Registry;

use ArrayIterator;
use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Exception\Collection\QueryTypeException;
use Netgen\BlockManager\Exception\RuntimeException;

class QueryTypeRegistry implements QueryTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryTypeInterface[]
     */
    protected $queryTypes = array();

    /**
     * Adds a query type to registry.
     *
     * @param string $type
     * @param \Netgen\BlockManager\Collection\QueryTypeInterface $queryType
     */
    public function addQueryType($type, QueryTypeInterface $queryType)
    {
        $this->queryTypes[$type] = $queryType;
    }

    /**
     * Returns if registry has a query type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasQueryType($type)
    {
        return isset($this->queryTypes[$type]);
    }

    /**
     * Returns a query type with provided identifier.
     *
     * @param string $type
     *
     * @throws \Netgen\BlockManager\Exception\Collection\QueryTypeException If query type does not exist
     *
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    public function getQueryType($type)
    {
        if (!$this->hasQueryType($type)) {
            throw QueryTypeException::noQueryType($type);
        }

        return $this->queryTypes[$type];
    }

    /**
     * Returns all query types.
     *
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface[]
     */
    public function getQueryTypes()
    {
        return $this->queryTypes;
    }

    /**
     * Retrieve an external iterator.
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->queryTypes);
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count()
    {
        return count($this->queryTypes);
    }

    /**
     * Whether a offset exists.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->hasQueryType($offset);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getQueryType($offset);
    }

    /**
     * Offset to set.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}
