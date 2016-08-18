<?php
namespace Clusterpoint\Transport;

use Clusterpoint\Response\Batch;
use Clusterpoint\Response\Single;
use Clusterpoint\Contracts\ConnectionInterface;
use Clusterpoint\Contracts\TransportInterface;
use Clusterpoint\Testing\ConnectionFaker;

/**
 *
 * Executes the request thrugh cURL to REST.
 *
 * @category   Clusterpoint 4.0 PHP Client API
 * @package    clusterpoint/php-client-api-v4
 * @copyright  Copyright (c) 2016 Clusterpoint (http://www.clusterpoint.com)
 * @author     Marks Gerasimovs <marks.gerasimovs@clusterpoint.com>
 * @license    http://opensource.org/licenses/MIT    MIT
 */
class Rest implements TransportInterface
{
    /**
     * Executes Query.
     *
     * @param  \stdClass $connection
     * @return \Clusterpoint\Response\Single|\Clusterpoint\Response\Batch|string
     */
    public static function execute(ConnectionInterface $connection, $forceSimpleUrl = false)
    {
    	$url = $connection->host.'/'.$connection->accountId.'/'.$connection->db.''.$connection->action.(isset($connection->transactionId) ? '?transaction_id='.$connection->transactionId : '');

		if ($forceSimpleUrl){
			$url = $connection->host.'/'.$connection->accountId.'/?user_account='.$connection->accountId;
		}

        if ($connection->debug === true) {
        	echo "URL: ".$url."\r\n";
        	echo "USER:PWD: ".$connection->username.":".str_repeat('X',strlen($connection->password))."\r\n";
        	echo "METHOD: ".$connection->method."\r\n";
        	echo "QUERY: ".(isset($connection->query) ? $connection->query : null)."\r\n";
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERPWD, $connection->username.":".$connection->password);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $connection->method);
        curl_setopt($curl, CURLOPT_POSTFIELDS, isset($connection->query) ? $connection->query : null);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $curlResponse = curl_exec($curl);

        if ($connection->debug === true) {
            if (curl_error($curl)) {
                echo "cURL error: ".curl_error($curl)."\r\n";
            }
            echo "RESPONSE: ".$curlResponse."\r\n";
        }

        curl_close($curl);

        return ($connection->query==='BEGIN_TRANSACTION') ? json_decode($curlResponse)->transaction_id : ((isset($connection->multiple) && $connection->multiple) ? new Batch($curlResponse, $connection) : new Single($curlResponse, $connection));
    }
}
