<?php

namespace Phinq;

use InvalidArgumentException;

class SkipQuery implements Query
{
	protected $amount;

	public function __construct($amount)
	{
		if (!is_int($amount)) {
			throw new InvalidArgumentException('1st argument must be an integer');
		}
			
		if ($amount < 0) {
			$amount--;
		}
			
		$this->amount = $amount;
	}

	public function execute(array $collection)
	{
		return array_slice($collection, $this->amount);
	}
}