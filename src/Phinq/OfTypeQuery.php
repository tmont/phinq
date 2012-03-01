<?php

namespace Phinq;

class OfTypeQuery implements Query
{

	protected $type;

	public function __construct($type)
	{
		$this->type = $type;
	}

	public function execute(array $collection)
	{
		$type = $this->type;
		
		return array_filter($collection, function($value) use ($type) {
			return $value instanceof $type;
		});
	}
}