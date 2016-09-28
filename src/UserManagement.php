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


	private function getValueByAlias($aliasArr, $options)
	{
		foreach ($aliasArr as $alias) {
			if (array_key_exists($alias, $options)) {
				return $options[$alias];
			}
		}

		return NULL; // not found
	}


	public function createGroup($name, $description = null, $account_id = null)
	{
		$this->connection->query = 'CREATE GROUP ' . $name;

		if (!is_null($description)) {
			$this->connection->query .= ' DESCRIPTION "' . addslashes($description) . '"';
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
		$query = array();
		$query[] = 'EDIT GROUP ' . $name;

		$options = array_change_key_case($options, CASE_LOWER);

		$alias = array('set_name', 'name');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'SET NAME ' . $value;
		}

		$alias = array('set_desc', 'set_description', 'desc', 'description');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'SET DESCRIPTION "' . addslashes($value) . '"';
		}

		$alias = array('set_role', 'set_roles');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'SET ROLES ' . (is_array($value) ? implode(', ', $value) : $value);
		}

		$alias = array('add_role', 'add_roles');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'ADD ROLES ' . (is_array($value) ? implode(', ', $value) : $value);
		}

		$alias = array('rem_role', 'rem_roles');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'REM ROLES ' . (is_array($value) ? implode(', ', $value) : $value);
		}

		$alias = array('clear_role', 'clear_roles');
		$value = $this->getValueByAlias($alias, $options);
		if ($value === true) {
			$query[] = 'CLEAR ROLES';
		}

		$alias = array('set_parent', 'set_parents');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'SET PARENTS ' . (is_array($value) ? implode(', ', $value) : $value);
		}

		$alias = array('add_parent', 'add_parents');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'ADD PARENTS ' . (is_array($value) ? implode(', ', $value) : $value);
		}

		$alias = array('rem_parent', 'rem_parents');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'REM PARENTS ' . (is_array($value) ? implode(', ', $value) : $value);
		}

		$alias = array('clear_parent', 'clear_parents');
		$value = $this->getValueByAlias($alias, $options);
		if ($value === true) {
			$query[] = 'CLEAR PARENTS';
		}

		$alias = array('disable_inheritance', 'disable_inherit');
		$value = $this->getValueByAlias($alias, $options);
		if ($value === true) {
			$query[] = 'DISABLE INHERITANCE';
		}

		$alias = array('enable_inheritance', 'enable_inherit');
		$value = $this->getValueByAlias($alias, $options);
		if ($value === true) {
			$query[] = 'ENABLE INHERITANCE';
		}

		$alias = array('account', 'in_account');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'IN ACCOUNT ' . $value;
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
			$this->connection->query .= ' DESCRIPTION "' . addslashes($description) . '"';
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
		$query = array();
		$query[] = 'EDIT ROLE ' . $name;

		$options = array_change_key_case($options, CASE_LOWER);

		$alias = array('set_name', 'name');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'SET NAME ' . $value;
		}

		$alias = array('set_desc', 'set_description', 'desc', 'description');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'SET DESCRIPTION "' . addslashes($value) . '"';
		}

		$alias = array('set_permissions', 'set_permission', 'set_perms', 'set_perm');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'SET PERMISSIONS ' . (is_array($value) ? implode(', ', $value) : $value);
		}

		$alias = array('add_permissions', 'add_permission', 'add_perms', 'add_perm');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'ADD PERMISSIONS ' . (is_array($value) ? implode(', ', $value) : $value);
		}

		$alias = array('rem_permissions', 'rem_permission', 'rem_perms', 'rem_perm');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'REM PERMISSIONS ' . (is_array($value) ? implode(', ', $value) : $value);
		}

		$alias = array('clear_permissions', 'clear_permission', 'clear_perms', 'clear_perm');
		$value = $this->getValueByAlias($alias, $options);
		if ($value === true) {
			$query[] = 'CLEAR PERMISSIONS';
		}

		$alias = array('account', 'in_account');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'IN ACCOUNT ' . $value;
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
		$options = array_change_key_case($options, CASE_LOWER);
		$query = array();

		$query[] = 'CREATE USER ' . $login;
		$query[] = 'IDENTIFIED BY "' . addslashes($password) . '"';

		$alias = array('gui', 'as gui user', 'as_gui_user');
		$value = $this->getValueByAlias($alias, $options);
		if ($value === true) {
			$query[] = 'AS GUI USER';
		}

		$alias = array('allowed_from', 'allowed from');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'ALLOWED FROM ' . (is_array($value) ? implode(',', $value) : $value);
		}

		$alias = array('name');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'WITH NAME "' . addslashes($value) . '"';
		}
		$alias = array('phone');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'WITH PHONE "' . addslashes($value) . '"';
		}
		$alias = array('timezone');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'WITH TIMEZONE "' . addslashes($value) . '"';
		}

		$alias = array('roles', 'role');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'WITH ROLES ' . (is_array($value) ? implode(', ', $value) : $value);
		}

		$alias = array('groups', 'group');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'WITH GROUPS ' . (is_array($value) ? implode(', ', $value) : $value);
		}

		$alias = array('account', 'in_account');
		$value = $this->getValueByAlias($alias, $options);
		if (!is_null($value)) {
			$query[] = 'IN ACCOUNT ' . $value;
		}


		$this->connection->query = implode(' ', $query);
		$response = DataLayer::execute($this->connection, true);
		$this->resetSelfManagement();
		return $response;
	}

}
