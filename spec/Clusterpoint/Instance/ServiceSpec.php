<?php
namespace spec\Clusterpoint\Instance;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Clusterpoint\Client;
use Clusterpoint\Helper\Test\ConnectionFaker;


class ServiceSpec extends ObjectBehavior
{
	//Where
    function it_is_initializable()
    {
    	$this->beConstructedWith(new ConnectionFaker);
        $this->shouldHaveType('Clusterpoint\Instance\Service');
    }
    
    function it_parses_where_field_only()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $connection = $this->where("name==\"Marks\"")->where("awesome==true")->get();
        $connection->query->shouldReturn('SELECT * FROM database WHERE name=="Marks" && awesome==true LIMIT 0, 20');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }

    function it_parses_where_clause_number()
    {
    	$this->beConstructedWith(new ConnectionFaker);
    	$connection = $this->where('age', 24)->get();
    	$connection->query->shouldReturn('SELECT * FROM database WHERE age==24 LIMIT 0, 20');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }
	
    function it_parses_where_clause_string()
    {
    	$this->beConstructedWith(new ConnectionFaker);
    	$connection = $this->where("name", "Marks")->get();
        $connection->query->shouldReturn('SELECT * FROM database WHERE name=="Marks" LIMIT 0, 20');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }

    function it_parses_or_where_clause_goes_first()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $connection = $this->orWhere("name", "Marks")->get();
        $connection->query->shouldReturn('SELECT * FROM database WHERE name=="Marks" LIMIT 0, 20');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }

    function it_parses_where_clause_boolean()
    {
    	$this->beConstructedWith(new ConnectionFaker);
    	$connection = $this->where("awesome", true)->get();
        $connection->query->shouldReturn('SELECT * FROM database WHERE awesome==true LIMIT 0, 20');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }

    function it_parses_where_clause_plus_or_where()
    {
    	$this->beConstructedWith(new ConnectionFaker);
    	$connection = $this->where("name","Marks")->orWhere("age", 24)->get();
        $connection->query->shouldReturn('SELECT * FROM database WHERE name=="Marks" || age==24 LIMIT 0, 20');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }
    
    function it_parses_where_clause_with_closure()
    {
    	$this->beConstructedWith(new ConnectionFaker);
    	$connection = $this->where(function ($query) {
			$query->where('name', "Marks")
				->where('awesome', true);
        	})->orWhere("gender", "male")->orWhere(function ($query) {
			$query->where('income_per_month', '>', 6)
				->where("company", 'Clusterpoint');
    	})->get();
        $connection->query->shouldReturn('SELECT * FROM database WHERE ( name=="Marks" && awesome==true ) || gender=="male" || ( income_per_month>6 && company=="Clusterpoint" ) LIMIT 0, 20');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }

    function it_parses_where_clause_with_zero()
    {
    	$this->beConstructedWith(new ConnectionFaker);
    	$connection = $this->where("price", 0)->orWhere("amount","==", 0)->get();
        $connection->query->shouldReturn('SELECT * FROM database WHERE price==0 || amount==0 LIMIT 0, 20');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }

    function it_parses_where_clause_with_CP_FIELD_and_RAW_and_prepend()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $connection = $this->prepend("function myFunc(age) { if (age>=18) { return true; } else { return false; } }")->where(Client::field("1==1"), true)->orWhere(Client::raw("myFunc(age)==true"))->get();
        $connection->query->shouldReturn('function myFunc(age) { if (age>=18) { return true; } else { return false; } } SELECT * FROM database WHERE this["1==1"]==true || myFunc(age)==true LIMIT 0, 20');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }

    // Select
    function it_parses_select_by_text()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $connection = $this->select("name, age,status")->get();
        $connection->query->shouldReturn('SELECT name, age,status FROM database LIMIT 0, 20');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }

    function it_parses_select_by_array()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $connection = $this->select(["name","age","status"])->get();
        $connection->query->shouldReturn('SELECT name, age, status FROM database LIMIT 0, 20');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }

    function it_parses_select_by_CP_FIELD()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $connection = $this->select([Client::field("1==1"),"age","status"])->get();
        $connection->query->shouldReturn('SELECT this["1==1"] as "1==1", age, status FROM database LIMIT 0, 20');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }

    // Order by
    function it_parses_order_by_1_param()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $connection = $this->orderBy("price")->get();
        $connection->query->shouldReturn('SELECT * FROM database ORDER BY price DESC LIMIT 0, 20');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }

    function it_parses_order_by_2_param()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $connection = $this->orderBy("price","asc")->get();
        $connection->query->shouldReturn('SELECT * FROM database ORDER BY price ASC LIMIT 0, 20');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }

    function it_parses_order_by_CP_FIELD()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $connection = $this->orderBy(Client::field("1==1"))->get();
        $connection->query->shouldReturn('SELECT * FROM database ORDER BY this["1==1"] DESC LIMIT 0, 20');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }

    //insert
    function it_parses_insert_one()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $connection = $this->insertOne(array("name" => "Marks", "surname"=>"Gerasimovs", "age"=>24));
        $connection->query->shouldReturn('{"name" : "Marks","surname" : "Gerasimovs","age" : 24}');
        $connection->action->shouldReturn('');
        $connection->method->shouldReturn('POST');
    }

    function it_parses_insert_many()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $connection = $this->insertMany(array("person_1" => array("name" => "Marks", "surname"=>"Gerasimovs", "age"=>24),"company1"=>array("name" => "Clusterpoint", "address"=>"Riga")));
        $connection->query->shouldReturn('[{"name":"Marks","surname":"Gerasimovs","age":24},{"name":"Clusterpoint","address":"Riga"}]');
        $connection->action->shouldReturn('');
        $connection->method->shouldReturn('POST');
    }

    // Update
    function it_parses_update_simple()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $connection = $this->update("_id", array("name" => "Marks"));
        $connection->query->shouldReturn('UPDATE database["_id"] SET name = "Marks"');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }

    function it_parses_update_multilevel()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $connection = $this->update("_id", array("name" => array("first" => "Marks", "last" => "Gerasimovs")));
        $connection->query->shouldReturn('UPDATE database["_id"] SET (typeof name != "undefined") ? name["first"] = "Marks" : name = {"first":"Marks","last":"Gerasimovs"}, (typeof name != "undefined") ?  name["last"] = "Gerasimovs" : name = {"first":"Marks","last":"Gerasimovs"}');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }

    // First
    function it_parses_first()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $connection = $this->first();
        $connection->query->shouldReturn('SELECT * FROM database LIMIT 0, 1');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }

    // Get
    function it_parses_get()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $connection = $this->get();
        $connection->query->shouldReturn('SELECT * FROM database LIMIT 0, 20');
        $connection->action->shouldReturn('/_query');
        $connection->method->shouldReturn('POST');
    }

    // Replace
    function it_parses_replace()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $connection = $this->replace("_id", array("name" => "Marks", "surname"=>"Gerasimovs", "age"=>24));
        $connection->query->shouldReturn('{"name" : "Marks","surname" : "Gerasimovs","age" : 24}');
        $connection->action->shouldReturn('[_id]');
        $connection->method->shouldReturn('PUT');
    }

    //Delete
    function it_parses_delete()
    {
        $this->beConstructedWith(new ConnectionFaker);
        $connection = $this->delete("_id");
        $connection->action->shouldReturn('[_id]');
        $connection->method->shouldReturn('DELETE');
    }

    //Transaction
    function it_parses_begin_transaction(){
        $this->beConstructedWith(new ConnectionFaker);
        $this->transaction()->shouldBeAnInstanceOf('\Clusterpoint\Instance\Service');
    }

}
