<?php
 
use DrewM\Drip\Drip;
use DrewM\Drip\Response;
use PHPUnit\Framework\TestCase;

 
class ResponseTest extends TestCase
{

	public function testBasicResponse()
	{
		$Response = new Response(['http_code'=>'200'], '[]');
		$this->assertEquals(200, $Response->status);
	}

	public function testResponseData()
	{
		$json = '{
				  "subscribers": [{
				    "email": "john@acme.com",
				    "time_zone": "America/Los_Angeles",
				    "custom_fields": {
				      "name": "John Doe"
				    }
				  }]
				}';

		$Response = new Response(['http_code'=>'200'], $json);

		$this->assertEquals('john@acme.com', $Response->subscribers[0]['email']);
	}

	public function testError()
	{
		$json = '{
				  "errors": [{
				    "code": "authorization_error",
				    "message": "You are not authorized to access this resource"
				  }]
				}';

		$Response = new Response(['http_code'=>'403'], $json);

		$this->assertEquals(403, $Response->status);
		$this->assertEquals('authorization_error', $Response->error);
		$this->assertEquals('You are not authorized to access this resource', $Response->message);
	}

	public function testStringification()
	{
		$json = '{
				  "subscribers": [{
				    "email": "john@acme.com",
				    "time_zone": "America/Los_Angeles",
				    "custom_fields": {
				      "name": "John Doe"
				    }
				  }]
				}';

		$Response = new Response(['http_code'=>'200'], $json);

		$this->assertEquals(print_r(json_decode($json, true), true), $Response.'');
	}

	public function testResponseArray()
	{
		$json = '{
				  "subscribers": [{
				    "email": "john@acme.com",
				    "time_zone": "America/Los_Angeles",
				    "custom_fields": {
				      "name": "John Doe"
				    }
				  }]
				}';

		$Response = new Response(['http_code'=>'200'], $json);

		$this->assertEquals(json_decode($json, true), $Response->get());
	}
}