<?php 
namespace Clusterpoint\Instance;

use Clusterpoint\Query\Builder as QueryBuilder;

class Service extends QueryBuilder
{
    /**
     * The stdClass with scope of connection access points.
     *
     * @var \stdClass
     */
    protected $connection;

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
