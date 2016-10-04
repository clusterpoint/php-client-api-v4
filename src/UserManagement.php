<?php
namespace Clusterpoint;


use Clusterpoint\Standart\Connection;
use Clusterpoint\Transport\Rest as DataLayer;

class UserManagement
{
	protected $connection;

	public function __construct($connection = "default")
	{
		$this->connection = new Connection($connection);
		$this->resetSelfManagement();
	}

	private function resetSelfManagement()
	{
		$this->connection->resetSelf();
		// set Management defaults
		$this->connection->query = '';
		$this->connection->method = 'POST';
		$this->connection->action = '/';
		$this->connection->multiple = true;
	}

	public function createGroup($name, $description = null, $account_id = null)
	{
		$this->connection->query = 'CREATE GROUP ' . $name;

		if (!is_null($description)) {
			$this->connection->query .= ' DESCRIPTION "' . Client::escape($description) . '"';
		}

		if (!is_null($account_id)) {
			$this->connection->query .= ' IN ACCOUNT ' . $account_id;
		}

		$response = DataLayer::execute($this->connection, true);
		$this->resetSelfManagement();
		return $response;
	}

	public function listGroups($account_id = null)
	{
		$this->connection->query = 'LIST GROUPS';

		if (!is_null($account_id)) {
			$this->connection->query .= ' IN ACCOUNT ' . $account_id;
		}

		$response = DataLayer::execute($this->connection, true);
		$this->resetSelfManagement();
		return $response;
	}

	public function getGroupInfo($name, $account_id = null)
	{
		$this->connection->query = 'LIST GROUP ' . $name;

		if (!is_null($account_id)) {
			$this->connection->query .= ' IN ACCOUNT ' . $account_id;
		}

		$this->connection->multiple = false;
		$response = DataLayer::execute($this->connection, true);
		$this->resetSelfManagement();
		return $response;
	}

	public function dropGroup($name, $account_id = null)
	{
		$this->connection->query = 'DROP GROUP ' . $name;

		if (!is_null($account_id)) {
			$this->connection->query .= ' IN ACCOUNT ' . $account_id;
		}

		$response = DataLayer::execute($this->connection, true);
		$this->resetSelfManagement();
		return $response;
	}

	public function editGroup($name, $options = array())
	{
		$optionsOrder = [
			0 => ['SET NAME'],
			1 => ['SET DESC', 'SET DESCRIPTION'],
			2 => ['SET ROLES', 'SET ROLE'],
			3 => ['ADD ROLES', 'ADD ROLE'],
			4 => ['REM ROLES', 'REM ROLE'],
			5 => ['CLEAR ROLES', 'CLEAR ROLE'],
			6 => ['SET PARENTS', 'SET PARENT'],
			7 => ['ADD PARENTS', 'ADD PARENT'],
			8 => ['REM PARENTS', 'REM PARENT'],
			9 => ['CLEAR PARENTS', 'CLEAR PARENT'],
			10 => ['DISABLE INHERITANCE', 'DISABLE INHERIT'],
			11 => ['ENABLE INHERITANCE', 'ENABLE INHERIT'],
			12 => ['IN ACCOUNT'],
		];

		$query = array();
		$query[] = 'EDIT GROUP ' . $name;

		$options = array_change_key_case($options, CASE_UPPER);

		foreach ($optionsOrder as $optAliasArr) {
			foreach ($optAliasArr as $alias) {
				if (array_key_exists($alias, $options)) {
					$value = $options[$alias];

					if ($alias === 'SET NAME') {
						$query[] = $alias . ' ' . $value; // special case
						break;
					}

					if ($alias === 'IN ACCOUNT') {
						$query[] = $alias . ' ' . (int)$value; // force int
						break;
					}

					if (is_array($value)) {
						$query[] = $alias . ' ' . implode(', ', $value);
						break;
					}

					if (is_bool($value)) {
						if ($value === true) {
							$query[] = $alias;
						}
						break;
					}

					$query[] = $alias . ' "' . Client::escape($value) . '"';
					break;
				}
			}
		}

		$this->connection->query = implode(' ', $query);

		$response = DataLayer::execute($this->connection, true);
		$this->resetSelfManagement();
		return $response;
	}


	public function createRole($name, $description = null, $account_id = null)
	{
		$this->connection->query = 'CREATE ROLE ' . $name;

		if (!is_null($description)) {
			$this->connection->query .= ' DESCRIPTION "' . Client::escape($description) . '"';
		}

		if (!is_null($account_id)) {
			$this->connection->query .= ' IN ACCOUNT ' . $account_id;
		}

		$response = DataLayer::execute($this->connection, true);
		$this->resetSelfManagement();
		return $response;
	}

	public function listRoles($account_id = null)
	{
		$this->connection->query = 'LIST ROLES';

		if (!is_null($account_id)) {
			$this->connection->query .= ' IN ACCOUNT ' . $account_id;
		}

		$response = DataLayer::execute($this->connection, true);
		$this->resetSelfManagement();
		return $response;
	}

	public function getRoleInfo($name, $account_id = null)
	{
		$this->connection->query = 'LIST ROLE ' . $name;

		if (!is_null($account_id)) {
			$this->connection->query .= ' IN ACCOUNT ' . $account_id;
		}

		$this->connection->multiple = false;
		$response = DataLayer::execute($this->connection, true);
		$this->resetSelfManagement();
		return $response;
	}

	public function dropRole($name, $account_id = null)
	{
		$this->connection->query = 'DROP ROLE ' . $name;

		if (!is_null($account_id)) {
			$this->connection->query .= ' IN ACCOUNT ' . $account_id;
		}

		$response = DataLayer::execute($this->connection, true);
		$this->resetSelfManagement();
		return $response;
	}

	public function editRole($name, $options = array())
	{
		$optionsOrder = [
			0 => ['SET NAME'],
			1 => ['SET DESC', 'SET DESCRIPTION'],
			2 => ['SET PERMISSIONS', 'SET PERMISSION', 'SET PERMS', 'SET PERM'],
			3 => ['ADD PERMISSIONS', 'ADD PERMISSION', 'ADD PERMS', 'ADD PERM'],
			4 => ['REM PERMISSIONS', 'REM PERMISSION', 'REM PERMS', 'REM PERM'],
			5 => ['CLEAR PERMISSIONS', 'CLEAR PERMISSION', 'CLEAR PERMS', 'CLEAR PERM'],
			6 => ['IN ACCOUNT'],
		];

		$query = array();
		$query[] = 'EDIT ROLE ' . $name;

		$options = array_change_key_case($options, CASE_UPPER);

		foreach ($optionsOrder as $optAliasArr) {
			foreach ($optAliasArr as $alias) {
				if (array_key_exists($alias, $options)) {
					$value = $options[$alias];

					if ($alias === 'SET NAME') {
						$query[] = $alias . ' ' . $value; // special case
						break;
					}

					if ($alias === 'IN ACCOUNT') {
						$query[] = $alias . ' ' . (int)$value; // force int
						break;
					}

					if (is_array($value)) {
						$query[] = $alias . ' ' . implode(', ', $value);
						break;
					}

					if (is_bool($value)) {
						if ($value === true) {
							$query[] = $alias;
						}
						break;
					}

					$query[] = $alias . ' "' . Client::escape($value) . '"';
					break;
				}
			}
		}

		$this->connection->query = implode(' ', $query);

		$response = DataLayer::execute($this->connection, true);
		$this->resetSelfManagement();
		return $response;
	}

	public function evalUser($name, $account_id = null)
	{
		$this->connection->query = 'EVALUATE PERMISSIONS FOR USER ' . $name;

		if (!is_null($account_id)) {
			$this->connection->query .= ' IN ACCOUNT ' . $account_id;
		}

		$response = DataLayer::execute($this->connection, true);
		$this->resetSelfManagement();
		return $response;
	}

	public function evalGroup($name, $account_id = null)
	{
		$this->connection->query = 'EVALUATE PERMISSIONS FOR GROUP ' . $name;

		if (!is_null($account_id)) {
			$this->connection->query .= ' IN ACCOUNT ' . $account_id;
		}

		$response = DataLayer::execute($this->connection, true);
		$this->resetSelfManagement();
		return $response;
	}


	public function listUsers($account_id = null)
	{
		$this->connection->query = 'LIST USERS';

		if (!is_null($account_id)) {
			$this->connection->query .= ' IN ACCOUNT ' . $account_id;
		}

		$response = DataLayer::execute($this->connection, true);
		$this->resetSelfManagement();
		return $response;
	}

	public function getUserInfo($login, $account_id = null)
	{
		$this->connection->query = 'LIST USER ' . $login;

		if (!is_null($account_id)) {
			$this->connection->query .= ' IN ACCOUNT ' . $account_id;
		}

		$this->connection->multiple = false;
		$response = DataLayer::execute($this->connection, true);
		$this->resetSelfManagement();
		return $response;
	}

	public function dropUser($login, $account_id = null)
	{
		$this->connection->query = 'DROP USER ' . $login;

		if (!is_null($account_id)) {
			$this->connection->query .= ' IN ACCOUNT ' . $account_id;
		}

		$response = DataLayer::execute($this->connection, true);
		$this->resetSelfManagement();
		return $response;
	}

	public function createUser($login, $password, $options = array())
	{
		$optionsOrder = [
			0 => ['AS GUI USER'],
			1 => ['ALLOWED FROM'],
			2 => ['WITH NAME'],
			3 => ['WITH PHONE'],
			4 => ['WITH TIMEZONE'],
			5 => ['WITH ROLES', 'WITH ROLE'],
			6 => ['WITH GROUPS', 'WITH GROUP'],
			7 => ['IN ACCOUNT'],
		];

		$query = array();
		$query[] = 'CREATE USER ' . $login;
		$query[] = 'IDENTIFIED BY "' . Client::escape($password) . '"';

		$options = array_change_key_case($options, CASE_UPPER);

		foreach ($optionsOrder as $optAliasArr) {
			foreach ($optAliasArr as $alias) {
				if (array_key_exists($alias, $options)) {
					$value = $options[$alias];

					if ($alias === 'SET NAME') {
						$query[] = $alias . ' ' . $value; // special case
						break;
					}

					if ($alias === 'IN ACCOUNT') {
						$query[] = $alias . ' ' . (int)$value; // force int
						break;
					}

					if (is_array($value)) {
						$query[] = $alias . ' ' . implode(', ', $value);
						break;
					}

					if (is_bool($value)) {
						if ($value === true) {
							$query[] = $alias;
						}
						break;
					}

					$query[] = $alias . ' "' . Client::escape($value) . '"';
					break;
				}
			}
		}

		$this->connection->query = implode(' ', $query);

		$response = DataLayer::execute($this->connection, true);
		$this->resetSelfManagement();
		return $response;
	}

	public function editUser($login, $options = array())
	{
		$optionsOrder = [
			0 => ['SET LOGIN'],
			1 => ['IDENTIFIED BY'],
			2 => ['SET AS GUI USER'],
			3 => ['SET ENABLED', 'SET DISABLED'],
			4 => ['SET ALLOWED FROM'],
			5 => ['SET NAME'],
			6 => ['SET PHONE'],
			7 => ['SET TIMEZONE'],
			8 => ['SET ROLES', 'SET ROLE'],
			9 => ['ADD ROLES', 'ADD ROLE'],
			10 => ['REM ROLES', 'REM ROLE'],
			11 => ['CLEAR ROLES', 'CLEAR ROLE'],
			12 => ['SET PARENTS', 'SET PARENT'],
			13 => ['ADD PARENTS', 'ADD PARENT'],
			14 => ['REM PARENTS', 'REM PARENT'],
			15 => ['CLEAR PARENTS', 'CLEAR PARENT'],
			16 => ['IN ACCOUNT'],
		];

		$query = array();
		$query[] = 'EDIT USER ' . $login;

		$options = array_change_key_case($options, CASE_UPPER);

		foreach ($optionsOrder as $optAliasArr) {
			foreach ($optAliasArr as $alias) {
				if (array_key_exists($alias, $options)) {
					$value = $options[$alias];

					if ($alias === 'SET LOGIN') {
						$query[] = $alias . ' ' . $value; // special case
						break;
					}

					if ($alias === 'IN ACCOUNT') {
						$query[] = $alias . ' ' . (int)$value; // force int
						break;
					}

					if (is_array($value)) {
						$query[] = $alias . ' ' . implode(', ', $value);
						break;
					}

					if (is_bool($value)) {
						if ($value === true) {
							$query[] = $alias;
						}
						break;
					}

					$query[] = $alias . ' "' . Client::escape($value) . '"';
					break;
				}
			}
		}

		$this->connection->query = implode(' ', $query);

		$response = DataLayer::execute($this->connection, true);
		$this->resetSelfManagement();
		return $response;
	}

}
