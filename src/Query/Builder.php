<?php 
namespace Clusterpoint\Query;

use Clusterpoint\Instance\Service;
use Closure;

abstract class Builder  implements BuilderInterface
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
    abstract public function __construct($connection);

    /**
     * Reset scope to standart values.
     *
     * @return void
     */
    protected function nullState()
    {
        $this->scope = new \stdClass;
        $this->scope->where = '';
        $this->scope->select = '*';
        $this->scope->limit = 20;
        $this->scope->offset = 0;
        $this->scope->orderBy = array();
        $this->scope->groupBy = array();
        $this->scope->prepend = '';
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
        try {
            if ($field instanceof Closure) {
                $this->scope->where .= $this->scope->where=='' ? ' (' : $logical.' (';
                call_user_func($field, $this);
                $this->scope->where .= ') ';
            } else {
                $logical = (strlen($this->scope->where) <=1 || substr($this->scope->where, -1)=='(') ? '' : $logical;
                $this->scope->where .= Parser::where($field, $operator, $value, $logical);
            }
        } catch (ClusterpointException $e) {
            if (isset($this->connection->transactionId)) {
                $this->rollback();
            }
            if ($this->connection->debug==true) {
                echo $e;
            }
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
        try {
            $this->scope->select = Parser::select($select);
        } catch (ClusterpointException $e) {
            if (isset($this->connection->transactionId)) {
                $this->rollback();
            }
            if ($this->connection->debug==true) {
                echo $e;
            }
        }
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
        try {
            $this->scope->limit = Parser::limit($limit);
        } catch (ClusterpointException $e) {
            if (isset($this->connection->transactionId)) {
                $this->rollback();
            }
            if ($this->connection->debug==true) {
                echo $e;
            }
        }
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
        try {
            $this->scope->offset = Parser::offset($offset);
        } catch (ClusterpointException $e) {
            if (isset($this->connection->transactionId)) {
                $this->rollback();
            }
            if ($this->connection->debug==true) {
                echo $e;
            }
        }
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
        try {
            $this->scope->orderBy[] = Parser::orderBy($field, $order);
        } catch (ClusterpointException $e) {
            if (isset($this->connection->transactionId)) {
                $this->rollback();
            }
            if ($this->connection->debug==true) {
                echo $e;
            }
        }
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
        try {
            $this->scope->groupBy[] = Parser::groupBy($field);
        } catch (ClusterpointException $e) {
            if (isset($this->connection->transactionId)) {
                $this->rollback();
            }
            if ($this->connection->debug==true) {
                echo $e;
            }
        }
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
        try {
            $this->scope->prepend = $prepend.' ';
        } catch (ClusterpointException $e) {
            if (isset($this->connection->transactionId)) {
                $this->rollback();
            }
            if ($this->connection->debug==true) {
                echo $e;
            }
        }
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
        try {
            $response = Parser::find($id, $this->connection);
        } catch (ClusterpointException $e) {
            if (isset($this->connection->transactionId)) {
                $this->rollback();
            }
            if ($this->connection->debug==true) {
                echo $e;
            }
        }
        return $response;
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
        $this->nullState();
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
        return Parser::raw($raw);
    }

    /**
     * Delete document by it's "_id" field.
     *
     * @param  string  $id
     * @return \Clusterpoint\Response\Single
     */
    public function delete($id = null)
    {
        try {
            $response = Parser::delete($id, $this->connection);
        } catch (ClusterpointException $e) {
            if (isset($this->connection->transactionId)) {
                $this->rollback();
            }
            if ($this->connection->debug==true) {
                echo $e;
            }
        }
        return $response;
    }

    /**
     * Insert one document.
     *
     * @param  mixed  $document
     * @return \Clusterpoint\Response\Single
     */
    public function insertOne($document)
    {
        try {
            $response = Parser::insertOne($document, $this->connection);
        } catch (ClusterpointException $e) {
            if (isset($this->connection->transactionId)) {
                $this->rollback();
            }
            if ($this->connection->debug==true) {
                echo $e;
            }
        }
        return $response;
    }

    /**
     * Insert batch of documents.
     *
     * @param  mixed  $document
     * @return \Clusterpoint\Response\Single
     */
    public function insertMany($document)
    {
        try {
            $response = Parser::insertMany($document, $this->connection);
        } catch (ClusterpointException $e) {
            if (isset($this->connection->transactionId)) {
                $this->rollback();
            }
            if ($this->connection->debug==true) {
                echo $e;
            }
        }
        return $response;
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
        try {
            $response = Parser::update($id, $document, $this->connection);
        } catch (ClusterpointException $e) {
            if (isset($this->connection->transactionId)) {
                $this->rollback();
            }
            if ($this->connection->debug==true) {
                echo $e;
            }
        }
        return $response;
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
        try {
            $response = Parser::replace($id, $document, $this->connection);
        } catch (ClusterpointException $e) {
            if (isset($this->connection->transactionId)) {
                $this->rollback();
            }
            if ($this->connection->debug==true) {
                echo $e;
            }
        }
        return $response;
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
        try {
            $response = Parser::rollbackTransaction($this->connection);
        } catch (ClusterpointException $e) {
            if ($this->connection->debug==true) {
                echo $e;
            }
        }
        return $response;
    }

    /**
     * Commit transaction.
     *
     * @return \Clusterpoint\Response\Single
     */
    public function commit()
    {
        try {
            $response = Parser::commitTransaction($this->connection);
        } catch (ClusterpointException $e) {
            if ($this->connection->debug==true) {
                echo $e;
            }
        }
        return $response;
    }
    
    /**
     * Reset query scope values to defaults.
     *
     * @return $this
     */
    public function resetQuery()
    {
        $this->nullState();
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
}
