<?php 
namespace Clusterpoint\Response;

use Clusterpoint\Query\Parser as QueryParser;

class Single extends Response
{
    /**
     * Holds "_id" of document.
     *
     * @var string
     */
    protected $_id;

    /**
     * Holds connection access points.
     *
     * @var object
     */
    protected $connection;

    /**
     * Reading data from inaccessible properties.
     *
     * @return object
     */
    public function &__get($key)
    {
        return $this->scope->results[$key];
    }

    /**
     * Writing data to inaccessible properties.
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->scope->results[$key] = $value;
    }

    /**
     * Save this documents with all changes made to it, to the database.
     *
     * @return \Clusterpoint\Response\Single
     */
    public function save()
    {
        return QueryParser::replace($this->_id, $this, $this->connection);
    }

    /**
     * Delete this documents from database.
     *
     * @return \Clusterpoint\Response\Single
     */
    public function delete()
    {
        return QueryParser::delete($this->_id, $this->connection);
    }
}
