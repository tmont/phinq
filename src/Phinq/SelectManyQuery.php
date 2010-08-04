<?php

	namespace Phinq;

	class SelectManyQuery extends LambdaDrivenQuery {

		public function execute(array $collection) {
			$flattenedCollection = array();
			$lambda = $this->getLambda();

			$this->walk($collection, function($key, $value) use (&$flattenedCollection, $lambda) {
				$flattenedCollection = array_merge($flattenedCollection, SelectManyQuery::nonRecursiveFlatten($lambda($value, $key)));
			});

			return $flattenedCollection;
		}

		public static function nonRecursiveFlatten($array) {
			$flattened = array();
			if (is_array($array)) {
				foreach ($array as $value) {
					$flattened[] = $value;
				}
			} else {
				$flattened[] = $array;
			}

			return $flattened;
		}
	}
	
?>