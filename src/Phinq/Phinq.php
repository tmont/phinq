<?php

	namespace Phinq;

	use Closure;

	class Phinq {

		private $collection;
		private $queryQueue = array();

		public function __construct(array $collection) {
			$this->collection = array_values($collection);
		}

		public static function create(array $collection) {
			return new static($collection);
		}

		public function toArray() {
			$collection = $this->collection;
			foreach ($this->queryQueue as $query) {
				$collection = $query->execute($collection);
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

		public function union(array $collectionToUnion, EqualityComparer $comparer = null) {
			$this->queryQueue[] = new UnionQuery($collectionToUnion, $comparer);
			return $this;
		}

		public function concat(array $collectionToConcat) {
			$this->queryQueue[] = new ConcatQuery($collectionToConcat);
			return $this;
		}
		
	}

?>
 
