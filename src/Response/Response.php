<?php 
namespace Clusterpoint\Response;

use Iterator;
use Countable;
use Clusterpoint\ConnectionInterface;
use Clusterpoint\Exceptions\ClusterpointException;

class Response implements Iterator, Countable, ResponseInterface
{
    /**
     * Holds response results and meta info.
     *
     * @var object
     */
    protected $scope;

    /**
     * Counter for \Iterator
     *
     * @var int
     */
    protected $position = 0;

    /**
     * Construct object to operate response data.
     *
     * @param  object  $raw_response
     * @param  object $connection
     * @return void
     */
    public function __construct($raw_response,ConnectionInterface $connection)
    {
        $this->scope = new \stdClass;
        $response = json_decode($raw_response);
        $this->scope = new Scope($response);
        $this->scope->rawResponse = $raw_response;
        $this->scope->query = isset($connection->query) ? $connection->query : null;
        $this->connection = clone($connection);
        $this->connection->resetSelf();
        if ($this instanceof Single) {
            $this->scope->results = isset($response->results) ? isset($response->results[0]) ? (array)$response->results[0] : array() : array();
            $this->_id = isset($this->scope->results["_id"]) ? $this->scope->results["_id"] : false;
        }
        if (isset($this->scope->error)) {
            foreach ($this->scope->error as $error) {
                if(!$connection instanceof \Clusterpoint\Testing\ConnectionFaker){
                    throw new ClusterpointException($error->message, $error->code);
                }
            }
        }
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return object
     */
    public function rewind()
    {
        return reset($this->scope->results);
    }
    
    /**
     * Return the current element
     *
     * @return object
     */
    public function current()
    {
        return current($this->scope->results);
    }

    /**
     * Return the key of the current element
     *
     * @return int
     */
    public function key()
    {
        return key($this->scope->results);
    }

    /**
     * Move forward to next element
     *
     * @return object
     */
    public function next()
    {
        return next($this->scope->results);
    }

    /**
     * Checks if current position is valid
     *
     * @return bool
     */
    public function valid()
    {
        return key($this->scope->results) !== null;
    }
    
    /**
     * Return the amount of elements in results
     *
     * @return int
     */
    public function count()
    {
        return count($this->scope->results);
    }

    /**
     * Returns the position of the last document that was returned
     *
     * @return int
     */
    public function to()
    {
        return $this->scope->to;
    }

    /**
     * Returns the position of the first document that was returned
     *
     * @return int
     */
    public function from()
    {
        return $this->scope->from;
    }

    /**
     * Returns the total number of hits - i.e. the number of documents in a storage that match the request
     *
     * @return int
     */
    public function hits()
    {
        return $this->scope->hits;
    }

    /**
     * Returns the left amount of documents, within the returned results.
     *
     * @return int
     */
    public function more()
    {
        return $this->scope->more;
    }

    /**
     * Returns array of errors occured.
     *
     * @return array
     */
    public function error()
    {
        return $this->scope->error;
    }

    /**
     * Returns the number of documents returned.
     *
     * @return int
     */
    public function found()
    {
        return $this->scope->found;
    }

    /**
     * Returns the time that it took to process the request in the CPS engine
     *
     * @return float
     */
    public function seconds()
    {
        return $this->scope->error;
    }

    /**
     * Returns the response JSON string.
     *
     * @return string
     */
    public function rawResponse()
    {
        return $this->scope->rawResponse;
    }

    /**
     * Returns the string with executed Query.
     *
     * @return string
     */
    public function executedQuery()
    {
        return $this->scope->query;
    }

    /**
     * Returns results as the array.
     *
     * @return string
     */
    public function toArray()
    {
        return $this->scope->results;
    }

    /**
     * Returns results as JSON string.
     *
     * @param bool $pretty
     * @return string
     */
    public function toJSON($pretty = false)
    {
        if ($pretty) {
            return json_encode($this->scope->results, JSON_PRETTY_PRINT);
        }
        return json_encode($this->scope->results);
    }
}
