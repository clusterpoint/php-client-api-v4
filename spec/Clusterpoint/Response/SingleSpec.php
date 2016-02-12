<?php
namespace spec\Clusterpoint\Response;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Clusterpoint\Helper\Test\ConnectionFaker;

/**
 * Clusterpoint/Response/Single Unit Testing
 *
 */
class SingleSpec extends ObjectBehavior
{
	protected $json = '{"results":[{"name":"Marks","age":24,"array":["1","2"],"_id":"id_string"}],"error":null,"seconds":0.064686,"hits":"40","more":"=30","found":"1","from":"0","to":"1"}';
    
    function it_is_initializable()
    {
    	$this->beConstructedWith($this->json, new ConnectionFaker);
        $this->shouldHaveType('Clusterpoint\Response\Single');
    }

    function it_parses_raw_response()
    {
    	$this->beConstructedWith($this->json, new ConnectionFaker);
        $this->rawResponse()->shouldReturn($this->json);
    }

    function it_parses__get_key()
    {
    	$this->beConstructedWith($this->json, new ConnectionFaker);
        $this->name->shouldReturn("Marks");
        $this->age->shouldReturn(24);
        $this->{"array"}->shouldReturn(["1","2"]);
    }

    function it_parses_toArray()
    {
    	$this->beConstructedWith($this->json, new ConnectionFaker);
        $this->toArray()->shouldReturn(["name" => "Marks","age"=>24, "array"=> ["1","2"],"_id"=>"id_string"]);
    }

    function it_parses_toJSON()
    {
    	$this->beConstructedWith($this->json, new ConnectionFaker);
        $this->toJSON()->shouldReturn('{"name":"Marks","age":24,"array":["1","2"],"_id":"id_string"}');
    }

    function it_parses_count()
    {
    	$this->beConstructedWith($this->json, new ConnectionFaker);
        $this->count()->shouldReturn(4);
    }

    function it_parses_save()
    {
    	$this->beConstructedWith($this->json, new ConnectionFaker);
    	$this->surname = "Gerasimovs";
        $connection =  $this->save();
        $connection->query->shouldReturn('{"name" : "Marks","age" : 24,"array" : ["1","2"],"_id" : "id_string","surname" : "Gerasimovs"}');
        $connection->method->shouldReturn('PUT');
        $connection->action->shouldReturn('[id_string]');
    }

    function it_parses_delete()
    {
    	$this->beConstructedWith($this->json, new ConnectionFaker);
        $connection = $this->delete();
        $connection->method->shouldReturn('DELETE');
        $connection->action->shouldReturn('[id_string]');
    }

    function it_parses_implemented_classes()
    {
    	$this->beConstructedWith($this->json, new ConnectionFaker);
    	$this->shouldImplement('Clusterpoint\Response\Response');
    	$this->shouldImplement('\Countable');
    	$this->shouldImplement('\Iterator');
    }

}
