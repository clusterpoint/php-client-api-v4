<?php 
namespace Clusterpoint\Helper\Test;

class ConnectionFaker
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
    public $accountUsername;

     /**
     * Holds fake Password.
     *
     * @var string
     */
    public $accountPassword;

     /**
     * Holds fake query.
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
        $this->accountUsername = "name";
        $this->accountPassword = "password";
        $this->debug = false;
        $this->query = "";
    }
}
