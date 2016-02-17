<?php 
namespace Clusterpoint\Instance;

use Exception;
use Clusterpoint\Contracts\ConnectionInterface;
use Clusterpoint\Query\Scope as QueryScope;
use Clusterpoint\Query\Builder as QueryBuilder;
use Clusterpoint\Exceptions\ClusterpointException;

/**
 *
 * Main Service class, holds meta data and executes query builder functions.
 *
 * @category   Clusterpoint 4.0 PHP Client API
 * @package    clusterpoint/php-client-api-v4
 * @copyright  Copyright (c) 2016 Clusterpoint (http://www.clusterpoint.com)
 * @author     Marks Gerasimovs <marks.gerasimovs@clusterpoint.com>
 * @license    http://opensource.org/licenses/MIT    MIT
 */
class Service extends QueryBuilder
{
    /**
     * Set connection access points.
     *
     * @param  \stdClass  $connection
     * @return void
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        $this->scope = new QueryScope;
    }

    /**
     * Wraps all method use in try - catch.
     *
     * @param  string  $method
     * @param  array  $arguments
     * @return $this
     */
    public function __call($method, $arguments)
    {
        $return = null;
        try {
            if (!in_array($method, get_class_methods('\Clusterpoint\Query\Builder'))) {
                throw new ClusterpointException("\"->{$method}()\" method: does not exist.", 9002);
            }
            $return = call_user_func_array([$this, $method], $arguments);
        } catch (Exception $e) {
            if (isset($this->connection->transactionId)) {
                $this->rollback();
            }
            if ($this->connection->debug==true) {
                echo $e;
            }
        }
        return $return;
    }
}
