<?php
 
use DrewM\Drip\Drip;
 
class WebhookTest extends PHPUnit_Framework_TestCase 
{

	/**
     * @dataProvider webhookProvider
     */
	public function testWebhookReceive($event_name, $event_json, $event_array)
	{
		$result   = Drip::receiveWebhook($event_json);
		$expected = $event_array;
		$this->assertEquals($expected, $result);
	}

	/**
     * @dataProvider webhookProvider
     */
	public function testWebhookSubscription($event_name, $event_json, $event_array)
	{
		$mock = $this->getMock('stdClass', array('myCallBack'));
		$mock->expects($this->once())->method('myCallBack')->with($this->equalTo($event_array['data']));

		Drip::subscribeToWebhook($event_name, [$mock, 'myCallBack']);
		Drip::receiveWebhook($event_json);
	}



	public function webhookProvider()
	{
		return [
			[
				'subscriber.applied_tag',
				'{"event":"subscriber.applied_tag","data":{"subscriber":{"id":"abc123","status":"active","email":"me@example.com","custom_fields":{"first_name":"J","last_name":"Smith"},"tags":["Spam"],"time_zone":"Europe/London","utc_offset":60,"created_at":"2015-06-23T14:47:36Z"},"properties":{"tag":"Spam"}}}',
				[
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
				]
			]

		];
	}

}