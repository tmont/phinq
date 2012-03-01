<?php

namespace Phinq;

class SelectManyQuery extends LambdaDrivenQuery
{
	public function execute(array $collection)
	{
		$flattenedCollection = array();
		$lambda = $this->getLambdaExpression();

		array_walk($collection, function($value, $key) use (&$flattenedCollection, $lambda) {
			$flattenedCollection = array_merge($flattenedCollection, Util::nonRecursiveFlatten($lambda($value)));
		});

		return $flattenedCollection;
	}
}