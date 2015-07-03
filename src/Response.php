<?php

namespace DrewM\Drip;

class Response
{
	public $status  = null;
	public $error   = null;
	public $message = null;

	private $data   = [];

	public function __construct($meta, $body)
	{
		if (isset($meta['http_code'])) {
			$this->status = (int) $meta['http_code'];	
		}
		
		$this->data = json_decode($body, true);

		if (is_array($this->data) && isset($this->data['errors'])) {
			$this->assign_errors();
		}
	}	

	public function __get($name)
	{
		if (is_array($this->data) && isset($this->data[$name])) {
			return $this->data[$name];
		}

		return false;
	}

	public function get()
	{
		return $this->data;
	}

	public function __toString()
	{
		return print_r($this->data, true);
	}

	public function __debugInfo() {
		return $this->data;
	}

	private function assign_errors()
	{
		if (isset($this->data['errors'][0]['code'])) {
			$this->error = $this->data['errors'][0]['code'];
		}

		if (isset($this->data['errors'][0]['message'])) {
			$this->message = $this->data['errors'][0]['message'];
		}
	}
}