<?php
namespace spec\Clusterpoint\Instance;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Clusterpoint\Client;
use Clusterpoint\Testing\ConnectionFaker;

/**
 * Clusterpoint/Instance/Servuce Unit Testing.
 *
 */
class ServiceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
    	$this->beConstructedWith(new ConnectionFaker);
        $this->shouldHaveType('Clusterpoint\Instance\Service');
    }
    
    function it_parses_implemented_classes()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $this->shouldImplement('Clusterpoint\Query\Builder');
    }

    function it_parses_where_field_only()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $response = $this->where("name==\"Marks\"")->where("awesome==true")->get();
        $response->executedQuery()->shouldReturn('SELECT * FROM database WHERE name=="Marks" && awesome==true LIMIT 0, 20');
    }

    // Where
    function it_parses_where_clause_number()
    {
    	$this->beConstructedWith(new ConnectionFaker);
    	$response = $this->where('age', 24)->get();
    	$response->executedQuery()->shouldReturn('SELECT * FROM database WHERE age==24 LIMIT 0, 20');
    }
	
    function it_parses_where_clause_string()
    {
    	$this->beConstructedWith(new ConnectionFaker);
    	$response = $this->where("name", "Marks")->get();
        $response->executedQuery()->shouldReturn('SELECT * FROM database WHERE name=="Marks" LIMIT 0, 20');
    }

    function it_parses_or_where_clause_goes_first()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $response = $this->orWhere("name", "Marks")->get();
        $response->executedQuery()->shouldReturn('SELECT * FROM database WHERE name=="Marks" LIMIT 0, 20');
    }

    function it_parses_where_clause_boolean()
    {
    	$this->beConstructedWith(new ConnectionFaker);
    	$response = $this->where("awesome", true)->get();
        $response->executedQuery()->shouldReturn('SELECT * FROM database WHERE awesome==true LIMIT 0, 20');
    }

    function it_parses_where_clause_plus_or_where()
    {
    	$this->beConstructedWith(new ConnectionFaker);
    	$response = $this->where("name","Marks")->orWhere("age", 24)->get();
        $response->executedQuery()->shouldReturn('SELECT * FROM database WHERE name=="Marks" || age==24 LIMIT 0, 20');
    }
    
    function it_parses_where_clause_with_closure()
    {
    	$this->beConstructedWith(new ConnectionFaker);
    	$response = $this->where(function ($query) {
			$query->where('name', "Marks")
				->where('awesome', true);
        	})->orWhere("gender", "male")->orWhere(function ($query) {
			$query->where('income_per_month', '>', 6)
				->where("company", 'Clusterpoint');
    	})->get();
        $response->executedQuery()->shouldReturn('SELECT * FROM database WHERE ( name=="Marks" && awesome==true ) || gender=="male" || ( income_per_month>6 && company=="Clusterpoint" ) LIMIT 0, 20');
    }

    function it_parses_where_clause_with_zero()
    {
    	$this->beConstructedWith(new ConnectionFaker);
    	$response = $this->where("price", 0)->orWhere("amount","==", 0)->get();
        $response->executedQuery()->shouldReturn('SELECT * FROM database WHERE price==0 || amount==0 LIMIT 0, 20');
    }

    function it_parses_where_clause_with_CP_FIELD_and_RAW_and_prepend()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $response = $this->prepend("function myFunc(age) { if (age>=18) { return true; } else { return false; } }")->where(Client::field("1==1"), true)->orWhere(Client::raw("myFunc(age)==true"))->get();
        $response->executedQuery()->shouldReturn('function myFunc(age) { if (age>=18) { return true; } else { return false; } } SELECT * FROM database WHERE this["1==1"]==true || myFunc(age)==true LIMIT 0, 20');
    }

    // Select
    function it_parses_select_by_text()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $response = $this->select("name, age,status")->get();
        $response->executedQuery()->shouldReturn('SELECT name, age,status FROM database LIMIT 0, 20');
    }

    function it_parses_select_by_array()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $response = $this->select(["name","age","status"])->get();
        $response->executedQuery()->shouldReturn('SELECT name, age, status FROM database LIMIT 0, 20');
    }

    function it_parses_select_by_CP_FIELD()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $response = $this->select([Client::field("1==1"),"age","status"])->get();
        $response->executedQuery()->shouldReturn('SELECT this["1==1"] as "1==1", age, status FROM database LIMIT 0, 20');
    }

    // Order by
    function it_parses_order_by_1_param()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $response = $this->orderBy("price")->get();
        $response->executedQuery()->shouldReturn('SELECT * FROM database ORDER BY price DESC LIMIT 0, 20');
    }

    function it_parses_order_by_2_param()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $response = $this->orderBy("price","asc")->get();
        $response->executedQuery()->shouldReturn('SELECT * FROM database ORDER BY price ASC LIMIT 0, 20');
    }

    function it_parses_order_by_CP_FIELD()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $response = $this->orderBy(Client::field("1==1"))->get();
        $response->executedQuery()->shouldReturn('SELECT * FROM database ORDER BY this["1==1"] DESC LIMIT 0, 20');
    }

    // Insert
    function it_parses_insert_one()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $response = $this->insertOne(array("name" => "Marks", "surname"=>"Gerasimovs", "age"=>24));
        $response->executedQuery()->shouldReturn('{"name" : "Marks","surname" : "Gerasimovs","age" : 24}');
        $response->shouldReturnAnInstanceOf('\Clusterpoint\Response\Single');
        $response->shouldBeAnInstanceOf('\Clusterpoint\Response\Response');
    }

    function it_parses_insert_many()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $response = $this->insertMany(array("person_1" => array("name" => "Marks", "surname"=>"Gerasimovs", "age"=>24),"company1"=>array("name" => "Clusterpoint", "address"=>"Riga")));
        $response->executedQuery()->shouldReturn('[{"name":"Marks","surname":"Gerasimovs","age":24},{"name":"Clusterpoint","address":"Riga"}]');
        $response->shouldReturnAnInstanceOf('\Clusterpoint\Response\Single');
        $response->shouldBeAnInstanceOf('\Clusterpoint\Response\Response');
    }

    // Update
    function it_parses_update_simple()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $response = $this->update("_id", array("name" => "Marks"));
        $response->executedQuery()->shouldReturn('UPDATE database["_id"] SET name = "Marks"');
        $response->shouldReturnAnInstanceOf('\Clusterpoint\Response\Single');
        $response->shouldBeAnInstanceOf('\Clusterpoint\Response\Response');
    }

    function it_parses_update_multilevel()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $response = $this->update("_id", array("name" => array("first" => "Marks", "last" => "Gerasimovs")));
        $response->executedQuery()->shouldReturn('UPDATE database["_id"] SET (typeof name != "undefined") ? name["first"] = "Marks" : name = {"first":"Marks","last":"Gerasimovs"}, (typeof name != "undefined") ?  name["last"] = "Gerasimovs" : name = {"first":"Marks","last":"Gerasimovs"}');
        $response->shouldReturnAnInstanceOf('\Clusterpoint\Response\Single');
        $response->shouldBeAnInstanceOf('\Clusterpoint\Response\Response');
    }

    // First
    function it_parses_first()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $response = $this->first();
        $response->executedQuery()->shouldReturn('SELECT * FROM database LIMIT 0, 1');
        $response->shouldReturnAnInstanceOf('\Clusterpoint\Response\Single');
        $response->shouldBeAnInstanceOf('\Clusterpoint\Response\Response');
        $response->shouldImplement('\Clusterpoint\Contracts\ResponseInterface');
    }

    // Get
    function it_parses_get()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $response = $this->get();
        $response->executedQuery()->shouldReturn('SELECT * FROM database LIMIT 0, 20');
        $response->shouldReturnAnInstanceOf('\Clusterpoint\Response\Batch');
        $response->shouldBeAnInstanceOf('\Clusterpoint\Response\Response');
        $response->shouldImplement('\Clusterpoint\Contracts\ResponseInterface');
    }

    // Replace
    function it_parses_replace()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $response = $this->replace("_id", array("name" => "Marks", "surname"=>"Gerasimovs", "age"=>24));
        $response->executedQuery()->shouldReturn('{"name" : "Marks","surname" : "Gerasimovs","age" : 24}');
        $response->shouldReturnAnInstanceOf('\Clusterpoint\Response\Single');
        $response->shouldBeAnInstanceOf('\Clusterpoint\Response\Response');
    }

    //Delete
    function it_parses_delete()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $response = $this->delete("_id");
        $response->shouldReturnAnInstanceOf('\Clusterpoint\Response\Single');
        $response->shouldBeAnInstanceOf('\Clusterpoint\Response\Response');
    }

}
