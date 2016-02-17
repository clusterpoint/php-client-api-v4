<?php 
namespace Clusterpoint\Query;

use Clusterpoint\Contracts\ScopeInterface;
/**
 *
 * Holds data for query exectuion.
 *
 * @category   Clusterpoint 4.0 PHP Client API
 * @package    clusterpoint/php-client-api-v4
 * @copyright  Copyright (c) 2016 Clusterpoint (http://www.clusterpoint.com)
 * @author     Marks Gerasimovs <marks.gerasimovs@clusterpoint.com>
 * @license    http://opensource.org/licenses/MIT    MIT
 */
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
