<?php

namespace Phinq;

class SelectQuery extends LambdaDrivenQuery
{
	public function execute(array $collection)
	{
		$newCollection = array();
		$lambda = $this->getLambdaExpression();

		array_walk($collection, function($value, $key) use (&$newCollection, $lambda) {
			$newCollection[] = $lambda($value);
		});

		return $newCollection;
	}
}