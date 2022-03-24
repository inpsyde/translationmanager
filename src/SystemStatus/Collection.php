<?php

namespace Translationmanager\SystemStatus;

use ArrayAccess;
use Countable;
use Iterator;

class Collection implements ArrayAccess, Iterator, Countable
{
    /**
     * @var array
     */
    private $list;

    /**
     * FilterContainer constructor
     *
     * @param array $list
     */
    public function __construct(array $list = [])
    {
        $this->list = $list;
    }

    /**
     * @return array The entire data list
     */
    public function asArray(): array
    {
        return $this->list;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->list[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->list[$offset];
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->list[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->list[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        return current($this->list);
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        next($this->list);
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return key($this->list);
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return isset($this->list[$this->key()]);
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        reset($this->list);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->list);
    }
}
