<?php
namespace Clusterpoint\Instance;

use Exception;
use Clusterpoint\Contracts\ConnectionInterface;
use Clusterpoint\Query\Scope as QueryScope;
use Clusterpoint\Query\Builder as QueryBuilder;
use Clusterpoint\Exceptions\ClusterpointException;
use Clusterpoint\Transport\Rest as DataLayer;


/**
 *
 * Main Service class, holds meta data and executes query builder functions.
 *
 * @category   Clusterpoint 4.0 PHP Client API
 * @package    clusterpoint/php-client-api-v4
 * @copyright  Copyright (c) 2016 Clusterpoint (http://www.clusterpoint.com)
 * @author     Marks Gerasimovs <marks.gerasimovs@clusterpoint.com>
 * @license    http://opensource.org/licenses/MIT    MIT
 */
class Service extends QueryBuilder
{
	/**
	 * Set connection access points.
	 *
	 * @param  \stdClass $connection
	 * @return void
	 */
	public function __construct(ConnectionInterface $connection)
	{
		$this->connection = $connection;
		$this->scope = new QueryScope;
	}

	public function createCollection($collectionName, $options = array()){
		$data = 'CREATE COLLECTION ' . $this->connection->db . '.' . $collectionName;

		if (isset($options['hyperreplication']) && $options['hyperreplication'] === true) {
			$data .= ' WITH HYPERREPLICATION ';
		}
		else {
			// do not send shards/replicas if hyperreplicated!
			if (isset($options['shards']) && $options['shards'] > 0) {
				$data .= ' WITH ' . $options['shards'] . ' SHARDS ';
			}
			if (isset($options['replicas']) && $options['replicas'] > 0) {
				$data .= ' WITH ' . $options['replicas'] . ' REPLICAS ';
			}
		}
		if (isset($options['dataModel']) && count($options['dataModel']) > 0) {
			$data .= ' WITH DATA MODEL ' . json_encode($options['dataModel']);
		}
		if (isset($options['config']) && count($options['config']) > 0) {
			$data .= ' WITH CONFIG ' . json_encode($options['config']);
		}

		$this->connection->query = $data;
		$this->connection->method = 'POST';
		$this->connection->multiple = true;
		$this->connection->action = '';

		$response = DataLayer::execute($this->connection, true);
		$this->connection->resetSelf();
		return $response;
	}

	public function editCollection($collectionName, $options = array())
	{
		$options = array_change_key_case($options, CASE_UPPER);

		$data = 'EDIT COLLECTION ' . $this->connection->db . '.' . $collectionName;

		foreach (array('IN ACCOUNT', 'IN_ACCOUNT', 'ACCOUNT') as $tmp) {
			if (isset($options[$tmp])) {
				$data .= ' IN ACCOUNT ' . $options[$tmp];
				unset($options[$tmp]);
			}
		}

		foreach (array('SET DATA MODEL', 'DATAMODEL') as $tmp) {
			if (isset($options[$tmp]) && count($options[$tmp]) > 0) {
				$data .= ' SET DATA MODEL ' . json_encode($options[$tmp]);
				unset($options[$tmp]);
			}
		}

		foreach (array('SET CONFIG', 'CONFIG') as $tmp) {
			if (isset($options[$tmp]) && count($options[$tmp]) > 0) {
				$data .= ' SET CONFIG ' . json_encode($options[$tmp]);
				unset($options[$tmp]);
			}
		}

		foreach (array('ENABLE DOCUMENT_LEVEL_SECURITY', 'ENABLE DOCUMENT LEVEL SECURITY', 'ENABLE DLS', 'DISABLE DOCUMENT_LEVEL_SECURITY', 'DISABLE DOCUMENT LEVEL SECURITY', 'DISABLE DLS') as $tmp) {
			if (isset($options[$tmp]) && $options[$tmp] === true) {
				$data .= ' ' . $tmp . ' ';
				unset($options[$tmp]);
			}
		}

		// everything remining in $options
		foreach ($options as $key => $val) {
			$data .= $key . ' ' . implode(', ', $val);
		}

		$this->connection->query = $data;
		$this->connection->method = 'POST';
		$this->connection->multiple = true;
		$this->connection->action = '';

		$response = DataLayer::execute($this->connection, true);
		$this->connection->resetSelf();
		return $response;
	}

	public function dropCollection($collectionName)
	{
		$data = 'DROP COLLECTION ' . $this->connection->db . '.' . $collectionName;

		$this->connection->query = $data;
		$this->connection->method = 'POST';
		$this->connection->multiple = true;
		$this->connection->action = '';

		$response = DataLayer::execute($this->connection, true);
		$this->connection->resetSelf();
		return $response;
	}

	public function clear()
	{
		$data = 'CLEAR COLLECTION ' . $this->connection->db;

		$this->connection->query = $data;
		$this->connection->method = 'POST';
		$this->connection->multiple = true;
		$this->connection->action = '';

		$response = DataLayer::execute($this->connection, true);
		$this->connection->resetSelf();
		return $response;
	}

	public function reindex($options = array())
	{
		$data = 'REINDEX COLLECTION ' . $this->connection->db;
		if (isset($options['inBackground'])) {
			$data .= ' IN BACKGROUND ';
		}
		if (isset($options['shard'])) {
			$data .= ' SHARD ' . $options['shard'];
		}
		if (isset($options['node'])) {
			$data .= ' NODE ' . $options['node'];
		}

		$this->connection->query = $data;
		$this->connection->method = 'POST';
		$this->connection->multiple = true;
		$this->connection->action = '';

		$response = DataLayer::execute($this->connection, true);
		$this->connection->resetSelf();
		return $response;
	}

	public function describe()
	{
		$data = 'DESCRIBE COLLECTION ' . $this->connection->db;

		$this->connection->query = $data;
		$this->connection->method = 'POST';
		$this->connection->multiple = true;
		$this->connection->action = '';

		$response = DataLayer::execute($this->connection, true);
		$this->connection->resetSelf();
		return $response;
	}



	/**
	 * Wraps all method use in try - catch.
	 *
	 * @param  string $method
	 * @param  array $arguments
	 * @return $this
	 */
	public function __call($method, $arguments)
	{
		$return = null;
		try {
			if (!in_array($method, $this->availableMethods())) {
				throw new ClusterpointException("\"->{$method}()\" method: does not exist.", 9002);
			}
			$return = call_user_func_array(array($this, $method), $arguments);
		} catch (Exception $e) {
			throw new ClusterpointException($e->getMessage(), $e->getCode());
		}
		return $return;
	}
}
