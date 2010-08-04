<?php

	namespace Phinq;

	class SelectManyQuery extends LambdaDrivenQuery {

		public function execute(array $collection) {
			$flattenedCollection = array();
			$lambda = $this->getLambda();

			$this->walk($collection, function($key, $value) use (&$flattenedCollection, $lambda) {
				$flattenedCollection = array_merge($flattenedCollection, Util::nonRecursiveFlatten($lambda($value, $key)));
			});

			return $flattenedCollection;
		}
		
	}
	
?>