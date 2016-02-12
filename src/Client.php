<?php
namespace Clusterpoint;

use Clusterpoint\Instance\Service;
use Clusterpoint\Helper\Key as ClusterpointKey;
use Clusterpoint\Helper\Raw as ClusterpointRaw;
use Clusterpoint\Exceptions\ClusterpointException;

/**
 * Clusterpoint PHP Client API for DB version 4.*
 *
 * PHP version 5.4
 *
 * @category   Library
 * @package    Clusterpoint PHP Client API v4
 * @author     Marks Gerasimovs <marks.gerasimovs@clusterpoint.com>
 * @copyright  2016 Clusterpoint Ltd.
 * @license    MIT
 * @version    4.0.1
 * @link       https://clusterpoint.com/docs/api/4/php
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
        if (gettype($connection)=="string") {
            $config = include(__DIR__.'/../../../../clusterpoint.php');
            $connection = $config[$connection];
        }
        $this->connection = new \stdClass;
        $this->connection->debug = isset($connection['debug']) ? $connection['debug'] : false;
        try {
            if (!isset($connection['account_id'])) {
                throw new ClusterpointException("Account ID is not set", 9004);
            } else {
                $this->connection->accountId = $connection['account_id'];
            }
            if (!isset($connection['username'])) {
                throw new ClusterpointException("Account username is not set", 9004);
            } else {
                $this->connection->accountUsername = $connection['username'];
            }
            if (!isset($connection['password'])) {
                throw new ClusterpointException("Account password is not set", 9004);
            } else {
                $this->connection->accountPassword = $connection['password'];
            }
            if (!isset($connection['host'])) {
                throw new ClusterpointException("Host is not set", 9004);
            } else {
                $this->connection->host = $connection['host'];
            }
        } catch (ClusterpointException $e) {
            if ($this->connection->debug==true) {
                echo $e;
            }
        }
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
