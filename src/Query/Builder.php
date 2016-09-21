<?php
namespace Clusterpoint\Query;

use Closure;
use Clusterpoint\Instance\Service;
use Clusterpoint\Contracts\ConnectionInterface;

/**
 *
 * Provides query builder functionality.
 *
 * @category   Clusterpoint 4.0 PHP Client API
 * @package    clusterpoint/php-client-api-v4
 * @copyright  Copyright (c) 2016 Clusterpoint (http://www.clusterpoint.com)
 * @author     Marks Gerasimovs <marks.gerasimovs@clusterpoint.com>
 * @license    http://opensource.org/licenses/MIT    MIT
 */
abstract class Builder
{
    /**
     * The stdClass with scope for query parametrs.
     *
     * @var \stdClass
     */
    protected $scope;

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
    abstract public function __construct(ConnectionInterface $connection);

	public function listWords($word, $field = null)
	{
		$this->where('word','==',$word);

		if (!is_null($field)){
			$this->scope->listWordsField = $field;
		}
		else {
			$this->scope->listWordsField = '';
		}

		return $this;
	}

	public function alternatives($word, $field = null)
	{
		$this->where('word','==',$word);

		if (!is_null($field)){
			$this->scope->alternativesField = $field;
		}
		else {
			$this->scope->alternativesField = '';
		}

		return $this;
	}

	public function getStatus()
	{
		return Parser::getStatus($this->connection);
	}

    /**
     * Add a basic where clause to the query.
     *
     * @param  string  $field
     * @param  string  $operator
     * @param  mixed   $value
     * @param  string  $logical
     * @return $this
     */
    public function where($field, $operator = null, $value = null, $logical = '&&')
    {
        if ($field instanceof Closure) {
            $this->scope->where .= $this->scope->where=='' ? ' (' : $logical.' (';
            call_user_func($field, $this);
            $this->scope->where .= ') ';
        } else {
            $logical = (strlen($this->scope->where) <=1 || substr($this->scope->where, -1)=='(') ? '' : $logical;
            $this->scope->where .= Parser::where($field, $operator, $value, $logical);
        }
        return $this;
    }

    /**
     * Add an "or where" clause to the query.
     *
     * @param  string  $field
     * @param  string  $operator
     * @param  mixed   $value
     * @return $this
     */
    public function orWhere($field, $operator = null, $value = null)
    {
        return $this->where($field, $operator, $value, '||');
    }

    /**
     * Set Select parametr to the query.
     *
     * @param  mixed  $select
     * @return $this
     */
    public function select($select = null)
    {
        $this->scope->select = Parser::select($select);
        return $this;
    }

    /**
     * Set Limit parametr to the query.
     *
     * @param  mixed  $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->scope->limit = Parser::limit($limit);
        return $this;
    }

    /**
     * Set Offset parametr to the query.
     *
     * @param  mixed  $offset
     * @return $this
     */
    public function offset($offset)
    {
        $this->scope->offset = Parser::offset($offset);
        return $this;
    }

    /**
     * Add "Order by" clause to the query.
     *
     * @param  string  $field
     * @param  string  $order
     * @return $this
     */
    public function orderBy($field, $order = null)
    {
        $this->scope->orderBy[] = Parser::orderBy($field, $order);
        return $this;
    }

    /**
     * Add "Group by" clause to the query.
     *
     * @param  string  $field
     * @return $this
     */
    public function groupBy($field)
    {
        $this->scope->groupBy[] = Parser::groupBy($field);
        return $this;
    }

	public function join($type)
	{
		$this->scope->join = $type;
		return $this;
	}

    /**
     * Set prepending text to the query.
     *
     * @param  string  $prepend
     * @return $this
     */
    public function prepend($prepend)
    {
        $this->scope->prepend .= $prepend.' ';
        return $this;
    }

    /**
     * Retrieve document by it's "_id" field.
     *
     * @param  string  $id
     * @return \Clusterpoint\Response\Single
     */
    public function find($id)
    {
        return Parser::find($id, $this->connection);
    }

    /**
     * Retrieve first document from the list executing the builded query.
     *
     * @return \Clusterpoint\Response\Single
     */
    public function first()
    {
        $this->scope->limit = 1;
        $this->scope->offset = 0;
        return $this->get(null);
    }

    /**
     * Retrieve batch of the documents executing the builded query.
     *
     * @param  boolean  $multiple
     * @return \Clusterpoint\Response\Batch
     */
    public function get($multiple = true)
    {
        $scope = $this->scope;
        return Parser::get($scope, $this->connection, $multiple);
    }

    /**
     * Get batch of documents executing the string passed as parametr.
     *
     * @param  string  $raw
     * @return \Clusterpoint\Response\Batch
     */
    public function raw($raw)
    {
        return Parser::raw($raw, $this->connection);
    }

    /**
     * Delete document by it's "_id" field.
     *
     * @param  string  $id
     * @return \Clusterpoint\Response\Single
     */
    public function delete($id = null)
    {
        return Parser::delete($id, $this->connection);
    }

    /**
     * Delete documents by "_id" fields.
     *
     * @param  $ids array
     * @return \Clusterpoint\Response\Single
     */
    public function deleteMany(array $ids = array())
    {
        return Parser::deleteMany($ids, $this->connection);
    }

    /**
     * Insert one document.
     *
     * @param  mixed  $document
     * @return \Clusterpoint\Response\Single
     */
    public function insertOne($document)
    {
        return Parser::insertOne($document, $this->connection);
    }

    /**
     * Insert batch of documents.
     *
     * @param  mixed  $document
     * @return \Clusterpoint\Response\Single
     */
    public function insertMany($document)
    {
        return Parser::insertMany($document, $this->connection);
    }

    /**
     * Update document by it's "_id".
     *
     * @param  string  $id
     * @param  mixed  $document
     * @return \Clusterpoint\Response\Single
     */
    public function update($id, $document  = null)
    {
        return Parser::update($id, $document, $this->connection);
    }

    /**
     * Replace document by it's "_id".
     *
     * @param  string  $id
     * @param  mixed  $document
     * @return \Clusterpoint\Response\Single
     */
    public function replace($id, $document = null)
    {
        return Parser::replace($id, $document, $this->connection);
    }

    /**
     * Start transaction.
     *
     * @return \Clusterpoint\Instance\Service
     */
    public function transaction()
    {
        $transaction_id = Parser::beginTransaction($this->connection);
        $connection = $this->connection;
        $connection->transactionId = $transaction_id;
        return new Service($connection);
    }

    /**
     * Rollback transaction.
     *
     * @return \Clusterpoint\Response\Single
     */
    public function rollback()
    {
        return Parser::rollbackTransaction($this->connection);
    }

    /**
     * Commit transaction.
     *
     * @return \Clusterpoint\Response\Single
     */
    public function commit()
    {
        return Parser::commitTransaction($this->connection);
    }

    /**
     * Reset query scope values to defaults.
     *
     * @return $this
     */
    public function resetQuery()
    {
        $this->scope->resetSelf();
        return $this;
    }

    /**
     * Get scope value by clause
     *
     * @return $scope
     */
    public function getQuery()
    {
        return Parser::get($this->scope, $this->connection, true, true);
    }
    /**
     * Get all available Methods
     *
     * @return $scope
     */
    public function availableMethods(){
        return get_class_methods($this);
    }
}
