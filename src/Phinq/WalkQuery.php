<?php

namespace Phinq;

class WalkQuery extends LambdaDrivenQuery
{
	public function execute(array $collection)
	{
		$lambda = $this->getLambdaExpression();
			
		array_walk($collection, function($value) use ($lambda) {
			$lambda($value);
		});
			
		return $collection;
	}
}