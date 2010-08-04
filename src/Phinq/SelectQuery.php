<?php

	namespace Phinq;

	class SelectQuery extends LambdaDrivenQuery {

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
