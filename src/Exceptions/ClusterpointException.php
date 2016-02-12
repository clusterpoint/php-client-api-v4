<?php
namespace Clusterpoint\Exceptions;

use Exception;

class ClusterpointException extends Exception
{
    /**
     * Whether object is accessed as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}.".PHP_EOL;
    }
}
