<?php 
namespace Clusterpoint\Transport;

interface TransportInterface
{
    /**
     * Executes Query.
     *
     * @param  \stdClass $connection
     * @return \Clusterpoint\Response\Single|\Clusterpoint\Response\Batch|string|\Clusterpint\Helper\Test\ConnectionFaker 
     */
    public static function execute(\Clusterpoint\ConnectionInterface $connection);
}
