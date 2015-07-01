<?php

namespace DrewM\Drip;

class Drip
{
	private static $eventSubscriptions = [];

	public static function subscribeToWebhook($event, callable $callback)
	{
		if (!isset(self::$eventSubscriptions[$event])) self::$eventSubscriptions[$event] = [];
		self::$eventSubscriptions[$event][] = $callback;
	}

	public static function receiveWebhook($input=null)
	{
		if (is_null($input)) {
			$input = file_get_contents("php://input");	
		}

		if ($input) {
			$result = json_decode($input, true);
			if ($result && isset($result['event'])) {
				self::disptachWebhookEvent($result['event'], $result['data']);
				return $result;
			}
		}		
		
		return false;
	}

	private static function disptachWebhookEvent($event, $data)
	{
		if (isset(self::$eventSubscriptions[$event])) {
			foreach(self::$eventSubscriptions[$event] as $callback) {
				$callback($data);
			}
			// reset subscriptions
			self::$eventSubscriptions[$event] = [];
		}
		return false;
	}
}