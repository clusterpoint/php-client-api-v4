<?php 
namespace Clusterpoint\Query;

class Scope implements ScopeInterface
{
    /**
     * Holds WHERE clause statements.
     *
     * @var array
     */
    public $where;

    /**
     * Holds SELECT clause statement.
     *
     * @var string
     */
    public $select;

     /**
     * Holds LIMIT statement.
     *
     * @var integer
     */
    public $limit;

     /**
     * Holds LIMIT statement.
     *
     * @var integer
     */
    public $offset;

     /**
     * Holds ORDER BY statement.
     *
     * @var string
     */
    public $orderBy;

     /**
     * Holds GROUP BY BY statement.
     *
     * @var string
     */
    public $groupBy;

     /**
     * Holds prepend for query.
     *
     * @var string
     */
    public $prepend;

    /**
     * Set Initial settings.
     *
     * @return void
     */
    public function __construct()
    {
        $this->resetSelf();
    }

    /**
     * Reset to default scope values settings.
     *
     * @return void
     */
    public function resetSelf()
    {
        $this->where = '';
        $this->select = '*';
        $this->limit = 20;
        $this->offset = 0;
        $this->orderBy = array();
        $this->groupBy = array();
        $this->prepend = '';
    }
}
