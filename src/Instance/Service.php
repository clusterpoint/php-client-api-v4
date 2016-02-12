<?php 
namespace Clusterpoint\Instance;

use Clusterpoint\Query\Builder as QueryBuilder;

class Service extends QueryBuilder
{
    /**
     * Set connection access points.
     *
     * @param  \stdClass  $connection
     * @return void
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
        $this->nullState();
    }
}
