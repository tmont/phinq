<?php

	namespace Phinq;

	class WalkExpression extends LambdaExpression {

		public function execute(array $collection) {
			$lambda = $this->getLambda();
			//abstracting the lambda function to avoid the possibility of the user passing by reference
			array_walk($collection, function($value) use ($lambda) { $lambda($value); });
			return $collection;
		}
	}
	
?>