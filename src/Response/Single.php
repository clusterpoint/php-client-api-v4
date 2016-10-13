<?php 
namespace Clusterpoint\Response;

use Clusterpoint\Query\Parser as QueryParser;
use Clusterpoint\Testing\ConnectionFaker;
use Clusterpoint\Exceptions\ClusterpointException;
/**
 *
 * Extends Response class, provides access to results of single document.
 *
 * @category   Clusterpoint 4.0 PHP Client API
 * @package    clusterpoint/php-client-api-v4
 * @copyright  Copyright (c) 2016 Clusterpoint (http://www.clusterpoint.com)
 * @author     Marks Gerasimovs <marks.gerasimovs@clusterpoint.com>
 * @license    http://opensource.org/licenses/MIT    MIT
 */
class Single extends Response
{
    /**
     * Holds "_id" of document.
     *
     * @var string
     */
    protected $_id = null;

    /**
     * Holds connection access points.
     *
     * @var object
     */
    protected $connection;

    /**
     * Checks if delete and save methods can be accessed.
     *
     * @param  string  $method
     * @param  array  $arguments
     * @return $this
     */
    public function __call($method, $arguments)
    {
        $methods = array("delete", "save");
        if (!$this->connection instanceof ConnectionFaker) {
            if (!in_array($method, $methods)) {
                throw new ClusterpointException("\"->{$method}()\" method: does not exist.", 9002);
                return $this;
            }
            if (!$this->scope->retrieved) {
                throw new ClusterpointException("\"->{$method}()\" method: can be accessed only to document retrieved by \"find()\" method.", 9002);
                return $this;
            }
        }
        return call_user_func_array(array($this, $method), $arguments);
    }

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

	public function __isset($name){
		return isset($this->scope->results[$name]);
	}

	public function __unset($name){
		unset($this->scope->results[$name]);
	}

    /**
     * Returns Connection access points
     *
     * @return \Clusterpoint\Response\Single
     */
    public function getConnection()
    {
        return $this->connection;
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
