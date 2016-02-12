<?php 
namespace Clusterpoint\Query;

/**
 * Interface for Clusterpoint\Query\Builder
 */
interface BuilderInterface
{
    /**
     * Add a basic where clause to the query.
     *
     * @param  string  $field
     * @param  string  $operator
     * @param  mixed   $value
     * @param  string  $logical
     * @return $this
     */
    public function where($field, $operator, $value, $logical);

    /**
     * Add an "or where" clause to the query.
     *
     * @param  string  $field
     * @param  string  $operator
     * @param  mixed   $value
     * @return $this
     */
    public function orWhere($field, $operator, $value);

    /**
     * Set Select parametr to the query.
     *
     * @param  mixed  $select
     * @return $this
     */
    public function select($select);

    /**
     * Set Limit parametr to the query.
     *
     * @param  mixed  $limit
     * @return $this
     */
    public function limit($limit);

    /**
     * Set Offset parametr to the query.
     *
     * @param  mixed  $offset
     * @return $this
     */
    public function offset($offset);

    /**
     * Add "Order by" clause to the query.
     *
     * @param  string  $field
     * @param  string  $order
     * @return $this
     */
    public function orderBy($field, $order);

    /**
     * Add "Group by" clause to the query.
     *
     * @param  string  $field
     * @return $this
     */
    public function groupBy($field);

    /**
     * Set prepending text to the query.
     *
     * @param  string  $prepend
     * @return $this
     */
    public function prepend($prepend);

    /**
     * Retrieve document by it's "_id" field.
     *
     * @param  string  $id
     * @return \Clusterpoint\Response\Single
     */
    public function find($id);

    /**
     * Retrieve first document from the list executing the builded query.
     *
     * @return \Clusterpoint\Response\Single
     */
    public function first();

    /**
     * Retrieve batch of the documents executing the builded query.
     *
     * @param  boolean  $multiple
     * @return \Clusterpoint\Response\Batch
     */
    public function get($multiple);

    /**
     * Get batch of documents executing the string passed as parametr.
     *
     * @param  string  $raw
     * @return \Clusterpoint\Response\Batch
     */
    public function raw($raw);

    /**
     * Delete document by it's "_id" field.
     *
     * @param  string  $id
     * @return \Clusterpoint\Response\Single
     */
    public function delete($id);

    /**
     * Insert one document.
     *
     * @param  mixed  $document
     * @return \Clusterpoint\Response\Single
     */
    public function insertOne($document);

    /**
     * Insert batch of documents.
     *
     * @param  mixed  $document
     * @return \Clusterpoint\Response\Single
     */
    public function insertMany($document);

    /**
     * Update document by it's "_id".
     *
     * @param  string  $id
     * @param  mixed  $document
     * @return \Clusterpoint\Response\Single
     */
    public function update($id, $document);

    /**
     * Replace document by it's "_id".
     *
     * @param  string  $id
     * @param  mixed  $document
     * @return \Clusterpoint\Response\Single
     */
    public function replace($id, $document);

    /**
     * Start transaction.
     *
     * @return \Clusterpoint\Instance\Service
     */
    public function transaction();

    /**
     * Rollback transaction.
     *
     * @return \Clusterpoint\Response\Single
     */
    public function rollback();

    /**
     * Commit transaction.
     *
     * @return \Clusterpoint\Response\Single
     */
    public function commit();

    /**
     * Reset query scope values to defaults.
     *
     * @return $this
     */
    public function resetQuery();
}
