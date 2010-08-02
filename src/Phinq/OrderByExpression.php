<?php

	namespace Phinq;

	use Closure;

	class OrderByExpression extends LambdaExpression {

		private $descending;

		public function __construct(Closure $lambda, $descending = false) {
			parent::__construct($lambda);
			$this->descending = (bool)$descending;
		}

		public function invoke(array $collection) {
			$lambda = $this->getLambda();

			uasort($collection, function($a, $b) use ($lambda) {
				$resultA = $lambda($a);
				$resultB = $lambda($b);

				if ($resultA == $resultB) {
					return 0;
				}

				return $resultA < $resultB ? -1 : 1;
			});

			if ($this->descending) {
				array_reverse($collection, true);
			}

			return $collection;
		}

	}
	
?>
