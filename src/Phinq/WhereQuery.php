<?php

namespace Phinq;

class WhereQuery extends LambdaDrivenQuery
{
	public function execute(array $collection)
	{
		$filteredCollection = array();
		$lambda = $this->getLambdaExpression();

		array_walk($collection, function($value, $key) use (&$filteredCollection, $lambda) {
			if ($lambda($value)) {
				$filteredCollection[] = $value;
			}
		});

		return $filteredCollection;
	}
}