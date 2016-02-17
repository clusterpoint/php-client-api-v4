<?php 
namespace Clusterpoint\Contracts;

use Clusterpoint\Contracts\ConnectionInterface;

/**
 *
 * Transport Layer interface.
 *
 * @category   Clusterpoint 4.0 PHP Client API
 * @package    clusterpoint/php-client-api-v4
 * @copyright  Copyright (c) 2016 Clusterpoint (http://www.clusterpoint.com)
 * @author     Marks Gerasimovs <marks.gerasimovs@clusterpoint.com>
 * @license    http://opensource.org/licenses/MIT    MIT
 */
interface TransportInterface
{
    /**
     * Executes Query.
     *
     * @param  \stdClass $connection
     * @return \Clusterpoint\Response\Single|\Clusterpoint\Response\Batch|string|\Clusterpint\Helper\Test\ConnectionFaker 
     */
    public static function execute(ConnectionInterface $connection);
}
