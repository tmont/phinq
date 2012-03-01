<?php

namespace Phinq;

class ReverseQuery implements Query
{
	public function execute(array $collection)
	{
		return array_reverse($collection);
	}
}