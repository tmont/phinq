<?php

	namespace Phinq;

	use Closure;

	class Phinq {

		private $collection;
		private $queryQueue = array();

		public function __construct(array $collection) {
			$this->collection = $collection;
		}

		public static function create(array $collection) {
			return new self($collection);	
		}

		public function toArray() {
			$collection = $this->collection;
			foreach ($this->queryQueue as $query) {
				$collection = $query->invoke($collection);
			}

			return $collection;
		}

		public function where(Closure $lambda) {
			$this->queryQueue[] = new WhereExpression($lambda);
			return $this;
		}

		public function orderBy(Closure $lambda, $descending = false) {
			$this->queryQueue[] = new OrderByExpression($lambda, (bool)$descending);
			return $this;
		}

		public function select(Closure $lambda) {
			$this->queryQueue[] = new SelectExpression($lambda);
			return $this;
		}
		
	}

?>
 
