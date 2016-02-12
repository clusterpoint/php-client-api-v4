<?php 
namespace Clusterpoint\Transport;

use Clusterpoint\Response\Batch as ResponseMultiple;
use Clusterpoint\Response\Single as ResponseSingle;

class Rest implements TransportInterface
{
    /**
     * Executes Query.
     *
     * @param  \stdClass $connection
     * @return \Clusterpoint\Response\Single|\Clusterpoint\Response\Batch|string 
     */
    public static function execute($connection)
    {
        // Unit Testing Purpose
        if ($connection instanceof \Clusterpoint\Helper\Test\ConnectionFaker) {
            return $connection;
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $connection->host.'/'.$connection->accountId.'/'.$connection->db.''.$connection->action.(isset($connection->transactionId) ? '?transaction_id='.$connection->transactionId : ''));
        curl_setopt($curl, CURLOPT_USERPWD, $connection->accountUsername.":".$connection->accountPassword);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $connection->method);
        curl_setopt($curl, CURLOPT_POSTFIELDS, isset($connection->query) ? $connection->query : null);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $curlResponse = curl_exec($curl);
        curl_close($curl);
        return ($connection->query=='BEGIN_TRANSACTION') ? json_decode($curlResponse)->{'transaction_id'} : ((isset($connection->multiple) && $connection->multiple) ? new ResponseMultiple($curlResponse) : new ResponseSingle($curlResponse, $connection));
    }
}
