<?php
 
use DrewM\Drip\Drip;
 
class WebhookTest extends PHPUnit_Framework_TestCase 
{
	public function testAttributeSelector()
	{
		$result = Drip::receiveWebhook('{"event":"subscriber.applied_tag","data":{"subscriber":{"id":"abc123","status":"active","email":"me@example.com","custom_fields":{"first_name":"J","last_name":"Smith"},"tags":["Spam"],"time_zone":"Europe/London","utc_offset":60,"created_at":"2015-06-23T14:47:36Z"},"properties":{"tag":"Spam"}}}');

		$expected = [
			'event' => 'subscriber.applied_tag',
			'data' => [
						'subscriber' => [
											'id' => 'abc123',
											'status' => 'active',
											'email' => 'me@example.com',
											'custom_fields' =>  [
																	'first_name' => 'J',
																	'last_name' => 'Smith'
																],
											'tags' => ['Spam'],
											'time_zone' => 'Europe/London',
											'utc_offset' => 60,
											'created_at' => '2015-06-23T14:47:36Z'
										],
							'properties' => [
								'tag' => 'Spam'
							]
						]
		];

		$this->assertEquals($expected, $result);
	}


}