<?php

	namespace Phinq;

	use Closure;

	class OrderByQuery extends LambdaDrivenQuery {

		private $descending;

		public function __construct(Closure $lambda, $descending = false) {
			parent::__construct($lambda);
			$this->descending = (bool)$descending;
		}

		public function execute(array $collection) {
			$lambda = $this->getLambda();

			usort($collection, function($a, $b) use ($lambda) {
				$resultA = $lambda($a);
				$resultB = $lambda($b);

				if ($resultA == $resultB) {
					return 0;
				}

				return $resultA < $resultB ? -1 : 1;
			});

			if ($this->descending) {
				$collection = array_reverse($collection);
			}

			return $collection;
		}

	}
	
?>
