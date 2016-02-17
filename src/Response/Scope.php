<?php 
namespace Clusterpoint\Response;

/**
 *
 * Holds Response meta data and result set.
 *
 * @category   Clusterpoint 4.0 PHP Client API
 * @package    clusterpoint/php-client-api-v4
 * @copyright  Copyright (c) 2016 Clusterpoint (http://www.clusterpoint.com)
 * @author     Marks Gerasimovs <marks.gerasimovs@clusterpoint.com>
 * @license    http://opensource.org/licenses/MIT    MIT
 */
class Scope
{
    /**
     * Holds rawResponse.
     *
     * @var string
     */
    public $rawResponse = null;

    /**
     * Holds "to" META-DATA.
     *
     * @var array
     */
    public $to = null;

    /**
     * Holds "from" META-DATA.
     *
     * @var string
     */
    public $from = null;

     /**
     * Holds "hits" META-DATA.
     *
     * @var integer
     */
    public $hits = null;

     /**
     * Holds "more" META-DATA.
     *
     * @var integer
     */
    public $more = null;

     /**
     * Holds "error" META-DATA.
     *
     * @var string
     */
    public $error = array();

     /**
     * Holds "found" META-DATA.
     *
     * @var string
     */
    public $found = null;

     /**
     * Holds "seconds" META-DATA.
     *
     * @var string
     */
    public $seconds = null;

     /**
     * Holds executed Query string.
     *
     * @var string
     */
    public $query = null;

     /**
     * Holds results
     *
     * @var string
     */
    public $results = array();

    /**
     * Set Initial settings.
     *
     * @return void
     */
    public function __construct($response)
    {
        foreach ($response as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
