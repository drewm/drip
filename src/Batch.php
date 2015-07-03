<?php

namespace DrewM\Drip;

class Batch extends Dataset
{
	public function jsonSerialize() {
        return [
        	'batches' => [
        		[
		        	$this->label => $this->data
		        ]
	        ]
        ];
    }

}