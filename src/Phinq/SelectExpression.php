<?php

	namespace Phinq;

	class SelectExpression extends LambdaExpression {

		public function execute(array $collection) {
			$newCollection = array();
			$lambda = $this->getLambda();

			$this->walk($collection, function($key, $value) use (&$newCollection, $lambda) {
				$newCollection[] = $lambda($value, $key);
			});

			return $newCollection;
		}
		
	}
	
?>
