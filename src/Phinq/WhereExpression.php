<?php

	namespace Phinq;

	class WhereExpression extends LambdaExpression {

		public function execute(array $collection) {
			$filteredCollection = array();
			$lambda = $this->getLambda();

			$this->walk($collection, function($key, $value) use (&$filteredCollection, $lambda) {
				if ($lambda($value, $key)) {
					$filteredCollection[] = $value;
				}
			});

			return $filteredCollection;
		}
		
	}
	
?>