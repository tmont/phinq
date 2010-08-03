<?php

	namespace Phinq;

	use Closure, OutOfBoundsException, RuntimeException;

	class Phinq {

		private $collection;
		private $queryQueue = array();

		public function __construct(array $collection) {
			$this->collection = array_values($collection);
		}

		/**
		 * @param  array $collection The initial collection to query on
		 * @return Phinq
		 */
		public final static function create(array $collection) {
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

		public function intersect(array $collectionToIntersect, EqualityComparer $comparer = null) {
			$this->queryQueue[] = new IntersectQuery($collectionToIntersect, $comparer);
			return $this;
		}

		public function concat(array $collectionToConcat) {
			$this->queryQueue[] = new ConcatQuery($collectionToConcat);
			return $this;
		}

		public function distinct(EqualityComparer $comparer = null) {
			$this->queryQueue[] = new DistinctQuery($comparer);
			return $this;
		}

		public function skip($amount) {
			$this->queryQueue[] = new SkipQuery($amount);
			return $this;
		}

		public function take($amount) {
			$this->queryQueue[] = new TakeQuery($amount);
			return $this;
		}

		public function first(Closure $lambda = null) {
			$first = $this->firstOrDefault($lambda);
			if ($first === null) {
				throw new OutOfBoundsException('Collection does not contain any elements');
			}

			return $first;
		}

		public function firstOrDefault(Closure $lambda = null) {
			$collection = $this->getCollection($lambda);

			if (empty($collection)) {
				return null;
			}

			return $collection[0];
		}

		public function single(Closure $lambda = null) {
			$single = $this->singleOrDefault($lambda);
			if ($single === null) {
				throw new RuntimeException('Collection does not contain exactly one element');
			}

			return $single;
		}

		public function singleOrDefault(Closure $lambda = null) {
			$collection = $this->getCollection($lambda);

			if (empty($collection)) {
				return null;
			}
			if (count($collection) !== 1) {
				throw new RuntimeException('Collection does not contain exactly one element');
			}

			return $collection[0];
		}

		public function last(Closure $lambda = null) {
			$last = $this->lastOrDefault($lambda);
			if ($last === null) {
				throw new OutOfBoundsException('Collection does not contain any elements');
			}

			return $last;
		}

		public function lastOrDefault(Closure $lambda = null) {
			$collection = $this->getCollection($lambda);

			if (empty($collection)) {
				return null;
			}

			return end($collection);
		}

		protected function getCollection(Closure $lambda = null) {
			$collection = $this->toArray();

			if ($lambda !== null) {
				$collection = self::create($collection)->where($lambda)->toArray();
			}

			return $collection;
		}

	}

?>