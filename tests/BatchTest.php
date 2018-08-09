<?php
 
use DrewM\Drip\Drip;
use DrewM\Drip\Batch;
use PHPUnit\Framework\TestCase;

 
class BatchTest extends TestCase
{

	public function testJsonSerialization()
	{
		$data = [];

		$data[] = [
					'email' => 'postmaster@example.com',
				];

		$data[] = [
					'email' => 'info@example.com',
				];

		$Batch = new Batch('subscribers', $data);

		$result = json_encode($Batch);

		$expected = json_encode([
			'batches'=>[
				[
					'subscribers' => [
		    			[
							'email' => 'postmaster@example.com',
						],
						[
							'email' => 'info@example.com',
						],
					]
				]
			]
			
		]);

		$this->assertEquals($expected, $result);

	}


}