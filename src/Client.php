<?php
namespace Clusterpoint;

use Clusterpoint\Instance\Service;
use Clusterpoint\Helper\Key as ClusterpointKey;
use Clusterpoint\Helper\Raw as ClusterpointRaw;
use Clusterpoint\Exceptions\ClusterpointException;
use Clusterpoint\Standart\Connection as StandartConnection;

/**
 *
 * Client for communicating between developer and our service.
 * Holds helper function raw(), field(), escape().
 * Resolves dependencies and starts API workflow.
 *
 * @category   Clusterpoint 4.0 PHP Client API
 * @package    clusterpoint/php-client-api-v4
 * @copyright  Copyright (c) 2016 Clusterpoint (http://www.clusterpoint.com)
 * @author     Marks Gerasimovs <marks.gerasimovs@clusterpoint.com>
 * @license    http://opensource.org/licenses/MIT    MIT
 */
class Client
{
    /**
     * The stdClass with scope of connection access points.
     *
     * @var \stdClass
     */
    protected $connection;

    /**
     * Validates connection for initializing service.
     *
     * @param  array|string  $connection
     * @return void
     */
    public function __construct($connection = "default")
    {
        $this->connection = class_exists("Clusterpoint\Connection") ? new Connection($connection) : new StandartConnection($connection);
    }

    /**
     * Escapes string for special characters.
     *
     * @param  string  $string
     * @return string
     */
    public static function escape($string)
    {
        $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
        $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
        return str_replace($search, $replace, $string);
    }

    /**
     * Creates Key Object for valid json key usage while quering.
     *
     * @param  string  $string
     * @return \Clusterpoint\Helper\Key
     */
    public static function field($string)
    {
        return new ClusterpointKey($string);
    }

    /**
     * Creates Raw Object for valid JSSQL command usage while quering.
     *
     * @param  string  $string
     * @return \Clusterpoint\Helper\Raw
     */
    public static function raw($string)
    {
        return new ClusterpointRaw($string);
    }

    /**
     * Creates Service instance that extends Query Builder.
     *
     * @param  string  $db
     * @return \Clusterpoint\Instance\Service
     */
    public function database($db)
    {
        $connection = $this->connection;
        $connection->db = $db;
        return new Service($connection);
    }
}
