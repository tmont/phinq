<?php

namespace Phinq;

class SkipWhileQuery extends LambdaDrivenQuery
{
	public function execute(array $collection)
	{
		$sliceIndex = -1;
		$predicate = $this->getLambdaExpression();
		while (array_key_exists(++$sliceIndex, $collection) && $predicate($collection[$sliceIndex])) {
			unset($collection[$sliceIndex]);
		}

		return array_values($collection);
	}
}