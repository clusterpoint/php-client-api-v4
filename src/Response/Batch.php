<?php 
namespace Clusterpoint\Response;

use ArrayAccess;

class Batch extends Response implements  ArrayAccess
{
    /**
     * Assign a value to the specified offset
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->scope->results[] = $value;
        } else {
            $this->scope->results[$offset] = $value;
        }
    }

    /**
     * Whether an offset exists
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->scope->results[$offset]);
    }

    /**
     * Unset an offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->scope->results[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @return int
     */
    public function offsetGet($offset)
    {
        return isset($this->scope->results[$offset]) ? $this->scope->results[$offset] : null;
    }
}
