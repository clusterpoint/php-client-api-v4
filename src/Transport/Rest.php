<?php 
namespace Clusterpoint\Transport;

use Clusterpoint\Response\Batch;
use Clusterpoint\Response\Single;
use Clusterpoint\ConnectionInterface;
use Clusterpoint\Testing\ConnectionFaker;

class Rest implements TransportInterface
{
    /**
     * Executes Query.
     *
     * @param  \stdClass $connection
     * @return \Clusterpoint\Response\Single|\Clusterpoint\Response\Batch|string|\Clusterpoint\Helper\Test\ConnectionFaker
     */
    public static function execute(ConnectionInterface $connection)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $connection->host.'/'.$connection->accountId.'/'.$connection->db.''.$connection->action.(isset($connection->transactionId) ? '?transaction_id='.$connection->transactionId : ''));
        curl_setopt($curl, CURLOPT_USERPWD, $connection->username.":".$connection->password);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $connection->method);
        curl_setopt($curl, CURLOPT_POSTFIELDS, isset($connection->query) ? $connection->query : null);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $curlResponse = curl_exec($curl);
        curl_close($curl);
        return ($connection->query==='BEGIN_TRANSACTION') ? json_decode($curlResponse)->transaction_id : ((isset($connection->multiple) && $connection->multiple) ? new Batch($curlResponse, $connection) : new Single($curlResponse, $connection));
    }
}
