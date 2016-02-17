<?php 
namespace Clusterpoint\Testing;

use Clusterpoint\Contracts\ConnectionInterface;

/**
 *
 * Acts as a Connection for Testing purposes.
 *
 * @category   Clusterpoint 4.0 PHP Client API
 * @package    clusterpoint/php-client-api-v4
 * @copyright  Copyright (c) 2016 Clusterpoint (http://www.clusterpoint.com)
 * @author     Marks Gerasimovs <marks.gerasimovs@clusterpoint.com>
 * @license    http://opensource.org/licenses/MIT    MIT
 */
class ConnectionFaker implements ConnectionInterface
{
    /**
     * Holds fake database name.
     *
     * @var string
     */
    public $db;

    /**
     * Holds fake host.
     *
     * @var string
     */
    public $host;

     /**
     * Holds fake Account ID.
     *
     * @var string
     */
    public $accountId;

     /**
     * Holds fake Username.
     *
     * @var string
     */
    public $username;

     /**
     * Holds fake Password.
     *
     * @var string
     */
    public $password;

    

     /**
     * Holds empty query for testing purposes.
     *
     * @var string
     */
    public $debug;

     /**
     * Holds empty query for testing purposes.
     *
     * @var string
     */
    public $query;
    
    /**
     * Set connection fake access points.
     *
     * @param  \stdClass  $connection
     * @return void
     */
    public function __construct()
    {
        $this->db = "database";
        $this->host = "https://api-eu.clusterpoint.com/v4";
        $this->accountId = "1";
        $this->username = "name";
        $this->password = "password";
        $this->debug = true;
        $this->query = null;
        $this->method = "GET";
        $this->action = "[id_string]";
    }

    public function resetSelf()
    {
    }
}
