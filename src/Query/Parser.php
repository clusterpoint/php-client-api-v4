<?php 
namespace Clusterpoint\Query;

use Clusterpoint\Transport\Rest as DataLayer;
use Clusterpoint\Exceptions\ClusterpointException;

class Parser
{

    /**
     * Pass back SELECT clause string to set in scope.
     *
     * @param  mixed  $select
     * @return string
     */
    public static function select($select)
    {
        if (gettype($select)=="array") {
            foreach ($select as $key => $field) {
                if ($field instanceof \Clusterpoint\Helper\Key) {
                    $alias =  '"'.$field.'"';
                    $field =  self::field($field);
                    $select[$key] = "{$field} as {$alias}";
                }
            }
            $select = implode(", ", $select);
        } elseif (gettype($select)!="string") {
            throw new ClusterpointException("\"->select()\" function: passed parametr is not in valid format.", 9002);
        }
        return $select;
    }

    /**
     * Pass back WHERE clause string to append the scope.
     *
     * @param  string  $field
     * @param  string  $operator
     * @param  mixed   $value
     * @param  string  $logical
     * @return string
     */
    public static function where($field, $operator, $value, $logical)
    {
        if (gettype($field)=="array") {
            throw new ClusterpointException("\"->where()\" function: passed field selector is not in valid format.", 9002);
        }
        if ($operator===null) {
            return "{$logical} {$field} ";
        } elseif ($value===null) {
            $value = $operator;
            $operator = '==';
        }
        if ($field instanceof \Clusterpoint\Helper\Key) {
            $field =  self::field("{$field}");
        }
        if (!($value instanceof \Clusterpoint\Helper\Raw)) {
            $value =  json_encode($value);
        }
        return "{$logical} {$field}{$operator}{$value} ";
    }

    /**
     * Pass back LIMIT parametr to set to scope.
     *
     * @param  mixed  $limit
     * @return int
     */
    public static function limit($limit)
    {
        if (!is_numeric($limit)) {
            throw new ClusterpointException("\"->limit()\" function: passed parametr is not in valid format.", 9002);
        }
        return intval($limit);
    }

    /**
     * Pass back OFFSET parametr to set to scope.
     *
     * @param  mixed  $offset
     * @return int
     */
    public static function offset($offset)
    {
        if (!is_numeric($offset)) {
            throw new ClusterpointException("\"->offset()\" function: passed parametr is not in valid format.", 9002);
        }
        return intval($offset);
    }

    /**
     * Pass back ORDER BY Clause to append the scope.
     *
     * @param  mixed  $field
     * @param  string $order
     * @return string
     */
    public static function orderBy($field, $order)
    {
        if (!$order) {
            $order = 'DESC';
        }
        $order = strtoupper($order);
        if (!($order=='ASC' || $order=='DESC')) {
            throw new ClusterpointException("\"->order()\" function: ordering should be DESC or ASC.", 9002);
        }
        if (!(gettype($field)=="string" || $field instanceof \Clusterpoint\Helper\Key || $field instanceof \Clusterpoint\Helper\Raw)) {
            throw new ClusterpointException("\"->order()\" function: passed field selector is not in valid format.", 9002);
        }
        if ($field instanceof \Clusterpoint\Helper\Key) {
            $field =  self::field("{$field}");
        }
        return "{$field} {$order}";
    }

    /**
     * Pass back GROUP BY Clause to append the scope.
     *
     * @param  mixed  $field
     * @return string
     */
    public static function groupBy($field)
    {
        if (!(gettype($field)=="string" || $field instanceof \Clusterpoint\Helper\Key || $field instanceof \Clusterpoint\Helper\Raw)) {
            throw new ClusterpointException("\"->group()\" function: passed field selector is not in valid format.", 9002);
        }
        if ($field instanceof \Clusterpoint\Helper\Key) {
            $field =  self::field("{$field}");
        }
        return "{$field}";
    }

    /**
     * Set query parametrs to execute - retrieve by "_id".
     *
     * @param  string  $id
     * @param  \stdClass  $connection
     * @return \Clusterpoint\Response\Single
     */
    public static function find($id = null, $connection)
    {
        if (gettype($id)!="string" && !is_numeric($id)) {
            throw new ClusterpointException("\"->find()\" function: \"_id\" is not in valid format.", 9002);
        }
        $connection->method = 'GET';
        $connection->action = '['.$id.']';
        return self::sendQuery($connection);
    }

    /**
     * Build query from scope. Passes to execute it. 
     *
     * @param  \stdClass  $scope
     * @param  \stdClass $connection
     * @param  bool $multiple
     * @return \Clusterpoint\Response\Batch
     */
    public static function get(\Clusterpoint\Query\Scope $scope, $connection, $multiple, $return = false)
    {
        $connection->query = $scope->prepend.'SELECT '.$scope->select.' FROM '.$connection->db.' ';
        if ($scope->where!='') {
            $connection->query .= 'WHERE'.$scope->where;
        }
        if (count($scope->groupBy)) {
            $connection->query .= 'GROUP BY '.implode(", ", $scope->groupBy).' ';
        }
        if (count($scope->orderBy)) {
            $connection->query .= 'ORDER BY '.implode(", ", $scope->orderBy).' ';
        }
        $connection->query .= 'LIMIT '.$scope->offset.', '.$scope->limit;
        if ($return) {
            return $connection->query;
        }
        $connection->method = 'POST';
        $connection->action = '/_query';
        $connection->multiple = $multiple;
        $scope->resetSelf();
        return self::sendQuery($connection);
    }
    
    /**
     * Passes raw query string for exectuion. 
     *
     * @param  string  $raw
     * @param  \stdClass $connection
     * @return \Clusterpoint\Response\Batch
     */
    public static function raw($raw, $connection)
    {
        $connection->query = $raw;
        $connection->method = 'POST';
        $connection->action = '/_query';
        $connection->multiple = true;
        return self::sendQuery($connection);
    }

    /**
     * Set query parametrs to execute - delete by "_id".
     *
     * @param  string  $id
     * @param  \stdClass $connection
     * @return \Clusterpoint\Response\Single
     */
    public static function delete($id = null, $connection)
    {
        if (gettype($id)!="string" && !is_numeric($id)) {
            throw new ClusterpointException("\"->delete()\" function: \"_id\" is not in valid format.", 9002);
        }
        $connection->method = 'DELETE';
        $connection->action = '['.$id.']';
        return self::sendQuery($connection);
    }

    /**
     * Set query document to execute - Insert One.
     *
     * @param  array|object  $document
     * @param  \stdClass $connection
     * @return \Clusterpoint\Response\Single
     */
    public static function insertOne($document, $connection)
    {
        $connection->query = self::singleDocument($document);
        return self::insert($connection);
    }

    /**
     * Set query documents to execute - Insert Many.
     *
     * @param  array|object  $document
     * @param  \stdClass $connection
     * @return \Clusterpoint\Response\Single
     */
    public static function insertMany($document, $connection)
    {
        if (gettype($document)!="array" && gettype($document)!="object") {
            throw new ClusterpointException("\"->insert()\" function: parametr passed ".json_encode(self::escape_string($document))." is not in valid document format.", 9002);
        }
        if (gettype($document)=="object") {
            $document_array = array();
            foreach ($document as $value) {
                $document_array[] = $value;
            }
            $document = $document_array;
        }
        $connection->query = json_encode(array_values($document));
        return self::insert($connection);
    }
    
    /**
     * Set query parametrs to execute - Insert.
     *
     * @param  array|object  $document
     * @param  \stdClass $connection
     * @return \Clusterpoint\Response\Single
     */
    public static function insert($connection)
    {
        $connection->method = 'POST';
        $connection->action = '';
        return self::sendQuery($connection);
    }
    
    /**
     * Set query parametrs to execute - Update by "_id".
     *
     * @param  string  $id
     * @param  array|object  $document
     * @param  \stdClass $connection
     * @return \Clusterpoint\Response\Single
     */
    public static function update($id, $document, $connection)
    {
        $connection->method = 'PATCH';
        $connection->action = '['.$id.']';
        switch (gettype($document)) {
            case "string":
                $connection->query = $document;
                break;
            case "array":
            case "object":
                $set_array = array();
                foreach ($document as $key => $value) {
                    switch (gettype($value)) {
                        case "array":
                        case "object":
                            $set_array[] = self::updateRecursion($key, $value);
                        break;
                        default:
                        $set_array[] = "{$key} = ".json_encode($value);
                    }
                }
                $connection->method = 'POST';
                $connection->action = '/_query';
                $connection->query = 'UPDATE '.$connection->db.'["'.$id.'"] SET '.implode(", ", $set_array);
                break;
            default:
                throw new ClusterpointException("\"->update()\" function: parametr passed ".json_encode(self::escape_string($document))." is not in valid format.", 9002);
                break;

        }
        return self::sendQuery($connection);
    }

    /**
     * Parse document for valid update command.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @param  string  $current_query
     * @param  string  $statement_end
     * @return string
     */
    public static function updateRecursion($key, $value, $current_query = '', $statement_end = '')
    {
        $query_string = '';
        switch (gettype($value)) {
            case "array":
            case "object":
                $query_string .= "(typeof {$key} != \"undefined\") ? ";
                $else_statement = " : {$key} = ".json_encode($value);
                $counter = 0;
                foreach ($value as $child_key => $child_value) {
                    if ($counter==0) {
                        $child_statement = self::updateRecursion($key."[\"{$child_key}\"]", $child_value, $query_string, $else_statement);
                        $first= false;
                    } else {
                        $child_statement .= ", ".$current_query.$query_string." ".self::updateRecursion($key."[\"{$child_key}\"]", $child_value, $query_string, $else_statement);
                    }
                    if (++$counter != count($value)) {
                        $child_statement .= " : {$key} = ".json_encode($value). $statement_end;
                    } else {
                        $query_string .= $child_statement;
                    }
                }
                $query_string .= $else_statement;
            break;
            default:
                $query_string .= "{$key} = ".json_encode($value);
        }
        return $query_string;
    }

    /**
     * Set query parametrs to execute - Replace by "_id".
     *
     * @param  string  $id
     * @param  array|object  $document
     * @param  \stdClass $connection
     * @return \Clusterpoint\Response\Single
     */
    public static function replace($id, $document, $connection)
    {
        $connection->query = self::singleDocument($document);
        $connection->method = 'PUT';
        $connection->action = '['.$id.']';
        return self::sendQuery($connection);
    }

    /**
     * Set query parametrs to execute - Begin Transaction.
     *
     * @param  \stdClass $connection
     * @return \Clusterpoint\Response\Single
     */
    public static function beginTransaction($connection)
    {
        $connection->query = 'BEGIN_TRANSACTION';
        $connection->method = 'POST';
        $connection->action = '/_query';
        return self::sendQuery($connection);
    }

    /**
     * Set query parametrs to execute - Rollback Transaction.
     *
     * @param  \stdClass $connection
     * @return \Clusterpoint\Response\Single
     */
    public static function rollbackTransaction($connection)
    {
        $connection->query = 'ROLLBACK';
        $connection->method = 'POST';
        $connection->action = '/_query';
        return self::sendQuery($connection);
    }

    /**
     * Set query parametrs to execute - Commit Transaction.
     *
     * @param  \stdClass $connection
     * @return \Clusterpoint\Response\Single
     */
    public static function commitTransaction($connection)
    {
        $connection->query = 'COMMIT';
        $connection->method = 'POST';
        $connection->action = '/_query';
        return self::sendQuery($connection);
    }

    /**
     * Escapes invalid for regular usage key values.
     *
     * @param  string $field
     * @return field
     */
    public static function field($field)
    {
        return 'this["'.$field.'"]' ;
    }

    /**
     * Pass query Params to Transport Layer for execution.
     *
     * @param  \stdClass $connection
     * @return mixed
     */
    public static function sendQuery(\Clusterpoint\ConnectionInterface $connection)
    {
        $response = DataLayer::execute($connection);
        $connection->resetSelf();
        return $response;
    }

    /**
     * Encode single document in valid format.
     *
     * @param  mixed $document
     * @return string
     */
    public static function singleDocument($document)
    {
        if (gettype($document)!="array" && gettype($document)!="object") {
            throw new ClusterpointException("\"->insert()\" function: parametr passed ".json_encode(self::escape_string($document))." is not in valid document format.", 9002);
        }
        $query = "{";
        $first = true;
        foreach ($document as $key => $value) {
            if (!$first) {
                $query .= ",";
            }
            $query .= '"'.self::escape_string($key).'" : '.json_encode($value);
            $first = false;
        }
        $query .= '}';
        return $query;
    }

    /**
     * Escapes string for special characters.
     *
     * @param  string $string
     * @return string
     */
    public static function escape_string($string)
    {
        $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
        $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
        return str_replace($search, $replace, $string);
    }
}
