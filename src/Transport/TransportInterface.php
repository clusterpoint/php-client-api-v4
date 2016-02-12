<?php 
namespace Clusterpoint\Transport;

interface TransportInterface
{
    /**
     * Executes Query.
     *
     * @param  \stdClass $connection
     * @return \Clusterpoint\Response\Single|\Clusterpoint\Response\Batch|string 
     */
    public static function execute($connection);
}
