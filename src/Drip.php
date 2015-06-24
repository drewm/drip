<?php

namespace DrewM\Drip;

class Drip
{


	public static function receiveWebhook($input=null)
	{
		if (is_null($input)) {
			$input = file_get_contents("php://input");	
		}

		if ($input) {
			return json_decode($input, true);	
		}		
		
		return false;
	}
}