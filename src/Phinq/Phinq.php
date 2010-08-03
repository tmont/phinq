<?php

	namespace Phinq;

	use Closure, OutOfBoundsException, BadMethodCallException, InvalidArgumentException;

	/**
	 * A port of .NET's LINQ extension methods
	 */
	class Phinq {

		private $collection;
		private $queryQueue = array();

		/**
		 * @param array $collection The initial collection to query on
		 */
		public function __construct(array $collection) {
			$this->collection = array_values($collection);
		}

		/**
		 * Convenience factory method for method chaining
		 *
		 * @param array $collection The initial collection to query on
		 * @return Phinq
		 */
		public final static function create(array $collection) {
			return new static($collection);
		}

		/**
		 * Executes the queries and returns the collection as an array
		 *
		 * @return array
		 */
		public function toArray() {
			$collection = $this->collection;
			foreach ($this->queryQueue as $query) {
				$collection = $query->execute($collection);
			}

			return $collection;
		}

		/**
		 * Filters the collection on a lambda expression
		 *
		 * The lambda expression takes one argument, the value of the current collection member,
		 * and returns a boolean indicating whether or not the member should be included in the
		 * filtered collection.
		 *
		 * @param Closure $lambda
		 * @return Phinq
		 */
		public function where(Closure $lambda) {
			$this->queryQueue[] = new WhereExpression($lambda);
			return $this;
		}

		/**
		 * Orders the collection on a lambda expression
		 *
		 * The lambda expression takes one argument, the value of the current collection member,
		 * and returns a value which will used to sort the entire collection.
		 *
		 * @param Closure $lambda
		 * @param bool $descending If true, the collection will be reversed
		 * @return Phinq
		 */
		public function orderBy(Closure $lambda, $descending = false) {
			$this->queryQueue[] = new OrderByExpression($lambda, (bool)$descending);
			return $this;
		}

		/**
		 * Maps each element of the collection to a new value
		 *
		 * The lambda expression takes one argument, the value of the current collection member,
		 * and returns a new value which replaces the original value in the collection.
		 *
		 * @param Closure $lambda
		 * @return Phinq
		 */
		public function select(Closure $lambda) {
			$this->queryQueue[] = new SelectExpression($lambda);
			return $this;
		}

		/**
		 * Performs a set union with the given collection
		 *
		 * @param array $collectionToUnion
		 * @param EqualityComparer $comparer
		 * @return Phinq
		 */
		public function union(array $collectionToUnion, EqualityComparer $comparer = null) {
			$this->queryQueue[] = new UnionQuery($collectionToUnion, $comparer);
			return $this;
		}

		/**
		 * Performs a set intersection with the given collection
		 *
		 * @param array $collectionToIntersect
		 * @param EqualityComparer $comparer
		 * @return Phinq
		 */
		public function intersect(array $collectionToIntersect, EqualityComparer $comparer = null) {
			$this->queryQueue[] = new IntersectQuery($collectionToIntersect, $comparer);
			return $this;
		}

		/**
		 * Concatenates the given collection to the end of the collection
		 *
		 * @param  $collectionToConcat
		 * @return Phinq
		 */
		public function concat(array $collectionToConcat) {
			$this->queryQueue[] = new ConcatQuery($collectionToConcat);
			return $this;
		}

		/**
		 * Removes duplicate values from the collection
		 *
		 * @param EqualityComparer $comparer
		 * @return Phinq
		 */
		public function distinct(EqualityComparer $comparer = null) {
			$this->queryQueue[] = new DistinctQuery($comparer);
			return $this;
		}

		/**
		 * Ignores the first $amount elements in the collection
		 *
		 * @param int $amount The amount of elements to skip, starting from index 0
		 * @return Phinq
		 */
		public function skip($amount) {
			$this->queryQueue[] = new SkipQuery($amount);
			return $this;
		}

		/**
		 * Takes only $amount elements from the collection, ignoring the remaining elements
		 *
		 * @param int $amount The number of elements to take
		 * @return Phinq
		 */
		public function take($amount) {
			$this->queryQueue[] = new TakeQuery($amount);
			return $this;
		}

		/**
		 * Gets the first element in the collection, or throws an exception if the collection
		 * is empty
		 *
		 * @throws EmptyCollectionException
		 * @param Closure $lambda Optional filter (see {@link where()})
		 * @return object
		 */
		public function first(Closure $lambda = null) {
			$first = $this->firstOrDefault($lambda);
			if ($first === null) {
				throw new EmptyCollectionException('Collection does not contain any elements');
			}

			return $first;
		}

		/**
		 * Gets the first element in the collection, or null if the collection is empty
		 *
		 * @param Closure $lambda Optional filter (see {@link where()})
		 * @return object|null The first element in the collection, or null if the collection is empty
		 */
		public function firstOrDefault(Closure $lambda = null) {
			$collection = $this->getCollection($lambda);

			if (empty($collection)) {
				return null;
			}

			return $collection[0];
		}

		/**
		 * Gets the only element in the collection, or throws an exception if there is not
		 * exactly one element in the collection
		 *
		 * @throws BadMethodCallException
		 * @param Closure $lambda Optional filter (see {@link where()})
		 * @return object
		 */
		public function single(Closure $lambda = null) {
			$single = $this->singleOrDefault($lambda);
			if ($single === null) {
				throw new BadMethodCallException('Collection does not contain exactly one element');
			}

			return $single;
		}

		/**
		 *
		 * Gets the only element in the collection, or null if the collection is empty, or throws
		 * an exception if there is not exactly one or zero elements in the collection
		 * 
		 * @throws BadMethodCallException
		 * @param Closure $lambda Optional filter (see {@link where()})
		 * @return object
		 */
		public function singleOrDefault(Closure $lambda = null) {
			$collection = $this->getCollection($lambda);

			if (empty($collection)) {
				return null;
			}
			if (count($collection) !== 1) {
				throw new BadMethodCallException('Collection does not contain exactly one element');
			}

			return $collection[0];
		}

		/**
		 * Gets the last element in the collection, or throws an exception if the collection is empty
		 *
		 * @throws EmptyCollectionException
		 * @param Closure $lambda Optional filter (see {@link where()})
		 * @return object
		 */
		public function last(Closure $lambda = null) {
			$last = $this->lastOrDefault($lambda);
			if ($last === null) {
				throw new EmptyCollectionException('Collection does not contain any elements');
			}

			return $last;
		}

		/**
		 * Gets the last element in the collection or null if the collection is empty
		 *
		 * @param Closure $lambda Optional filter (see {@link where()})
		 * @return object
		 */
		public function lastOrDefault(Closure $lambda = null) {
			$collection = $this->getCollection($lambda);

			if (empty($collection)) {
				return null;
			}

			return end($collection);
		}

		/**
		 * Gets the element at the specified index
		 *
		 * If $index is negative, gets the element at the specified index from the end.
		 *
		 * @throws EmptyCollectionException|InvalidArgumentException|OutOfBoundsException
		 * @param int $index
		 * @return object
		 */
		public function elementAt($index) {
			if (!is_int($index)) {
				throw new InvalidArgumentException('1st argument must be an integer');	
			}

			$collection = $this->getCollection();
			if (empty($collection)) {
				throw new EmptyCollectionException('Collection contains no elements');
			}

			$count = count($collection);
			if ($index < 0) {
				$index = $count + $index;
			}

			if ($index >= $count || $index < 0) {
				throw new OutOfBoundsException('Collection does not contain an element at index ' . $index);
			}

			return $collection[$index];
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