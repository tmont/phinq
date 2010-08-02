<?php

	namespace Phinq;

	class SelectExpression extends LambdaExpression {

		public function invoke(array $collection) {
			$newCollection = array();
			$lambda = $this->getLambda();

			$this->walk($collection, function($key, $value) use (&$newCollection, $lambda) {
				$newCollection[$key] = $lambda($value, $key);
			});

			return $newCollection;
		}
		
	}
	
?>
