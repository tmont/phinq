<?php

	namespace Phinq;

	use Closure;

	abstract class LambdaExpression implements Expression {

		private $lambda;

		public function __construct(Closure $lambda) {
			$this->lambda = $lambda;
		}

		public final function getLambda() {
			return $this->lambda;	
		}

		protected final function walk(array $collection, Closure $lambda) {
			array_walk($collection, function($value, $key) use ($lambda) { $lambda($key, $value); });
		}

	}
	
?>
