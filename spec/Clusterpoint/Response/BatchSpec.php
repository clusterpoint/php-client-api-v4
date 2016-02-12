<?php
namespace spec\Clusterpoint\Response;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Clusterpoint\Helper\Test\ConnectionFaker;

/**
 * Clusterpoint/Response/Batch Unit Testing
 *
 */
class BatchSpec extends ObjectBehavior
{
	protected $json = '{"results":[{"name":"Marks","age":24,"city":"Riga","_id":"_id_1"},{"name":"Clusterpoint","age":10,"city":"Riga","_id":"_id_2"}],"error":null,"seconds":0.129516,"hits":"40","more":"=21","found":"2","from":"0","to":"2"}';
    
    function it_is_initializable()
    {
    	$this->beConstructedWith($this->json, new ConnectionFaker);
        $this->shouldHaveType('Clusterpoint\Response\Batch');
    }

    function it_parses_raw_response()
    {
    	$this->beConstructedWith($this->json, new ConnectionFaker);
        $this->rawResponse()->shouldReturn($this->json);
    }
    
    function it_parses_toJSON()
    {
    	$this->beConstructedWith($this->json, new ConnectionFaker);
        $this->toJSON()->shouldReturn('[{"name":"Marks","age":24,"city":"Riga","_id":"_id_1"},{"name":"Clusterpoint","age":10,"city":"Riga","_id":"_id_2"}]');
    }

    function it_parses_count()
    {
    	$this->beConstructedWith($this->json, new ConnectionFaker);
        $this->count()->shouldReturn(2);
    }


    function it_parses_implemented_classes()
    {
    	$this->beConstructedWith($this->json, new ConnectionFaker);
    	$this->shouldImplement('Clusterpoint\Response\Response');
    	$this->shouldImplement('\Countable');
    	$this->shouldImplement('\Iterator');
    	$this->shouldImplement('\ArrayAccess');
    }
}
