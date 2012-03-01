<?php

namespace Phinq;

use InvalidArgumentException, OutOfBoundsException;

class TakeQuery implements Query
{
	protected $amount;

	public function __construct($amount)
	{
		if (!is_int($amount)) {
			throw new InvalidArgumentException('1st argument must be an integer');
		}
		
		if ($amount < 0) {
			throw new OutOfBoundsException('Take amount must be greater than or equal to zero');
		}

		$this->amount = $amount;
	}

	public function execute(array $collection)
	{
		return array_slice($collection, 0, $this->amount);
	}
}