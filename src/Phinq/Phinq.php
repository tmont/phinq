<?php

	namespace Phinq;

	use Iterator, Closure, OutOfBoundsException, BadMethodCallException, InvalidArgumentException;

	/**
	 * A port of .NET's LINQ extension methods
	 */
	class Phinq implements Iterator {

		private $collection;
		private $evaluatedCollection;
		private $queryQueue = array();
		private $index = 0;
		private $isDirty = false;

		/**
		 * @param array $collection The initial collection to query on
		 */
		public function __construct(array $collection) {
			$this->collection = array_values($collection);
			$this->evaluatedCollection = $this->collection;
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

		protected function getCollection(Closure $predicate = null) {
			$collection = $this->toArray();

			if ($predicate !== null) {
				$collection = self::create($collection)->where($predicate)->toArray();
			}

			return $collection;
		}

		protected final function addToQueue(Query $query) {
			$this->queryQueue[] = $query;
			$this->isDirty = true;
		}

		/**
		 * Executes the queries and returns the collection as an array
		 *
		 * @return array
		 */
		public function toArray() {
			if ($this->isDirty || $this->evaluatedCollection === null) {
				$this->index = 0;
				$this->isDirty = false;
				$this->evaluatedCollection = $this->collection;
				foreach ($this->queryQueue as $query) {
					$this->evaluatedCollection = $query->execute($this->evaluatedCollection);
				}
			}

			return $this->evaluatedCollection;
		}

		/**
		 * Filters the collection using the given predicate
		 *
		 * The lambda expression takes one argument, the value of the current collection member,
		 * and returns a boolean indicating whether or not the member should be included in the
		 * filtered collection.
		 *
		 * @param Closure $predicate
		 * @return Phinq
		 */
		public function where(Closure $predicate) {
			$this->addToQueue(new WhereExpression($predicate));
			return $this;
		}

		/**
		 * Orders the collection using the given lambda expression to determine sort index
		 *
		 * The lambda expression takes one argument, the value of the current collection member,
		 * and returns a value which will used to sort the entire collection.
		 *
		 * @param Closure $lambda
		 * @param bool $descending If true, the collection will be reversed
		 * @return Phinq
		 */
		public function orderBy(Closure $lambda, $descending = false) {
			$this->addToQueue(new OrderByExpression($lambda, (bool)$descending));
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
			$this->addToQueue(new SelectExpression($lambda));
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
			$this->addToQueue(new UnionQuery($collectionToUnion, $comparer));
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
			$this->addToQueue(new IntersectQuery($collectionToIntersect, $comparer));
			return $this;
		}

		/**
		 * Concatenates the given collection to the end of the collection
		 *
		 * @param  $collectionToConcat
		 * @return Phinq
		 */
		public function concat(array $collectionToConcat) {
			$this->addToQueue(new ConcatQuery($collectionToConcat));
			return $this;
		}

		/**
		 * Removes duplicate values from the collection
		 *
		 * @param EqualityComparer $comparer
		 * @return Phinq
		 */
		public function distinct(EqualityComparer $comparer = null) {
			$this->addToQueue(new DistinctQuery($comparer));
			return $this;
		}

		/**
		 * Bypasses the first $amount elements in the collection
		 *
		 * @param int $amount The amount of elements to skip, starting from index 0
		 * @return Phinq
		 */
		public function skip($amount) {
			$this->addToQueue(new SkipQuery($amount));
			return $this;
		}

		/**
		 * Bypasses all elements as long as the given predicate is satisfied
		 *
		 * @param Closure $predicate Takes one argument, the current element, and returns a boolean
		 * @return Phinq
		 */
		public function skipWhile(Closure $predicate) {
			$this->addToQueue(new SkipWhileExpression($predicate));
			return $this;
		}

		/**
		 * Takes only $amount elements from the collection, ignoring the remaining elements
		 *
		 * @param int $amount The number of elements to take
		 * @return Phinq
		 */
		public function take($amount) {
			$this->addToQueue(new TakeQuery($amount));
			return $this;
		}

		/**
		 * Returns elements as long as the given predicate is satisfied
		 *
		 * @param Closure $predicate Takes one argument, the current element, and returns a boolean
		 * @return Phinq
		 */
		public function takeWhile(Closure $predicate) {
			$this->addToQueue(new TakeWhileExpression($predicate));
			return $this;
		}

		/**
		 * Gets the first element in the collection, or throws an exception if the collection
		 * is empty
		 *
		 * @throws EmptyCollectionException
		 * @param Closure $predicate Optional filter (see {@link where()})
		 * @return object
		 */
		public function first(Closure $predicate = null) {
			$first = $this->firstOrDefault($predicate);
			if ($first === null) {
				throw new EmptyCollectionException('Collection does not contain any elements');
			}

			return $first;
		}

		/**
		 * Gets the first element in the collection, or null if the collection is empty
		 *
		 * @param Closure $predicate Optional filter (see {@link where()})
		 * @return object|null The first element in the collection, or null if the collection is empty
		 */
		public function firstOrDefault(Closure $predicate = null) {
			$collection = $this->getCollection($predicate);

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
		 * @param Closure $predicate Optional filter (see {@link where()})
		 * @return object
		 */
		public function single(Closure $predicate = null) {
			$single = $this->singleOrDefault($predicate);
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
		 * @param Closure $predicate Optional filter (see {@link where()})
		 * @return object
		 */
		public function singleOrDefault(Closure $predicate = null) {
			$collection = $this->getCollection($predicate);

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
		 * @param Closure $predicate Optional filter (see {@link where()})
		 * @return object
		 */
		public function last(Closure $predicate = null) {
			$last = $this->lastOrDefault($predicate);
			if ($last === null) {
				throw new EmptyCollectionException('Collection does not contain any elements');
			}

			return $last;
		}

		/**
		 * Gets the last element in the collection or null if the collection is empty
		 *
		 * @param Closure $predicate Optional filter (see {@link where()})
		 * @return object
		 */
		public function lastOrDefault(Closure $predicate = null) {
			$collection = $this->getCollection($predicate);

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
		 * @throws OutOfBoundsException
		 * @param int $index
		 * @return object
		 */
		public function elementAt($index) {
			$element = $this->elementAtOrDefault($index);
			if ($element === null) {
				throw new OutOfBoundsException('Collection does not contain an element at index ' . $index);
			}

			return $element;
		}

		/**
		 * Gets the element at the specified index or null if the collection does not contain
		 * an element at that index
		 *
		 * If $index is negative, gets the element at the specified index from the end.
		 *
		 * @throws InvalidArgumentException
		 * @param int $index
		 * @return object|null
		 */
		public function elementAtOrDefault($index) {
			if (!is_int($index)) {
				throw new InvalidArgumentException('1st argument must be an integer');
			}

			$collection = $this->getCollection();
			if (empty($collection)) {
				return null;
			}

			$count = count($collection);
			if ($index < 0) {
				$index = $count + $index;
			}

			if ($index >= $count || $index < 0) {
				return null;
			}

			return $collection[$index];
		}

		/**
		 * Groups the collection into a collection of {@link Grouping}s based on
		 * the given lambda expression
		 *
		 * $lambda takes in one argument, the current element, and returns the key
		 * that determines how the collection is grouped.
		 *
		 * @param Closure $lambda
		 * @return Phinq
		 */
		public function groupBy(Closure $lambda) {
			$this->addToQueue(new GroupByExpression($lambda));
			return $this;
		}

		/**
		 * Verifies that every element in the collection satisfies the given predicate
		 *
		 * $predicate takes in one argument, the current element, and returns a boolean.
		 * Note that if the collection is empty, this method evaluates to true.
		 *
		 * @param Closure $predicate
		 * @return bool
		 */
		public function all(Closure $predicate) {
			return array_reduce($this->toArray(), function($current, $next) use ($predicate) { return $current && $predicate($next); }, true);
		}

		/**
		 * Verifies that any element in the collection satisifes the given predicate
		 *
		 * $predicate takes in one argument, the current element, and returns a boolean.
		 *
		 * @param Closure $predicate
		 * @return bool
		 */
		public function any(Closure $predicate = null) {
			$collection = $this->toArray();
			if ($predicate === null && !empty($collection)) {
				return true;
			}

			foreach ($collection as $value) {
				if ($predicate($value)) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Verifies that the collection contains the specified value
		 *
		 * @param mixed $value The value to check for
		 * @param EqualityComparer $comparer
		 * @return bool
		 */
		public function contains($value, EqualityComparer $comparer = null) {
			$comparer = $comparer ?: DefaultEqualityComparer::getInstance();
			foreach ($this->toArray() as $element) {
				if ($comparer->equals($value, $element) === 0) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Counts the number of elements in the collection, optionally filtered by the
		 * given predicate
		 *
		 * $predicate should take one argument, the current element, and return a boolean.
		 *
		 * @param Closure $predicate
		 * @return int
		 */
		public function count(Closure $predicate = null) {
			$collection = $this->getCollection($predicate);
			return count($collection);
		}

		/**
		 * Reverses the elements in the collection
		 *
		 * @return Phinq
		 */
		public function reverse() {
			$this->addToQueue(new ReverseQuery());
			return $this;
		}

		/**
		 * Gets the maximum-valued element from the collection
		 *
		 * This method is equivalent to calling orderBy($lambda, true) followed by firstOrDefault().
		 *
		 * @return mixed
		 */
		public function max(Closure $lambda = null) {
			$lambda = $lambda ?: function($value) { return $value; };
			return static::create($this->toArray())->orderBy($lambda, true)->firstOrDefault();
		}

		/**
		 * Gets the minimum-valued element from the collection
		 *
		 * This method is equivalent to calling orderBy($lambda) followed by firstOrDefault().
		 *
		 * @return mixed
		 */
		public function min(Closure $lambda = null) {
			$lambda = $lambda ?: function($value) { return $value; };
			return static::create($this->toArray())->orderBy($lambda)->firstOrDefault();
		}

		/**
		 * Computes the average value of all values in the collection
		 *
		 * Note that this always returns a float, so if the collection is not
		 * contained entirely of numeric values, $lambda should be a transform
		 * function that maps each element to a numeric value. Otherwise, the result
		 * may be unexpected.
		 *
		 * @param Closure $lambda
		 * @return float Returns zero if the collection is empty
		 */
		public function average(Closure $lambda = null) {
			$collection = $lambda !== null ? Phinq::create($this->toArray())->select($lambda)->toArray() : $this->toArray();
			if (empty($collection)) {
				return 0;
			}

			return array_sum($collection) / count($collection);
		}

		/**
		 * Compures the sum of all values in the collection
		 *
		 * Note that this always returns a float, so if the collection is not
		 * contained entirely of numeric values, $lambda should be a transform
		 * function that maps each element to a numeric value. Otherwise, the result
		 * may be unexpected.
		 *
		 * @param Closure $lambda
		 * @return float
		 */
		public function sum(Closure $lambda = null) {
			$collection = $lambda !== null ? Phinq::create($this->toArray())->select($lambda)->toArray() : $this->toArray();
			return array_sum($collection);
		}

		/**
		 * Reduces the collection to a single value
		 *
		 * Example:
		 * <code>
		 * factorial = Phinq::create(array(1, 2, 3, 4, 5))
		 *   ->aggregate(function($current, $next) { return $current * $next; }, 1);
		 * </code>
		 *
		 * @see array_reduce()
		 *
		 * @param Closure $accumulator Takes two values, the current value and the next value, and returns the input to the next iteration
		 * @param mixed $seed Optional seed for the accumulator, or the default value if the collection is empty
		 * @return mixed
		 */
		public function aggregate(Closure $accumulator, $seed = null) {
			$collection = $this->toArray();
			return array_reduce($collection, $accumulator, $seed);
		}

		/**
		 * Computes the set difference, i.e. all elements in the collection that are not
		 * in $collectionToExcept
		 *
		 * @param array $collectionToExcept
		 * @param EqualityComparer $comparer
		 * @return Phinq
		 */
		public function except(array $collectionToExcept, EqualityComparer $comparer = null) {
			$this->addToQueue(new ExceptQuery($collectionToExcept, $comparer));
			return $this;
		}

		/**
		 * Flattens a collection of collections into a single collection
		 *
		 * $lambda takes in one argument, the current element, and returns an array.
		 *
		 * @param Closure $lambda
		 * @return Phinq
		 */
		public function selectMany(Closure $lambda) {
			$this->addToQueue(new SelectManyExpression($lambda));
			return $this;
		}

		/**
		 * Determines whether two collections are equal, element for element
		 *
		 * @param array $otherCollection
		 * @param EqualityComparer $comparer
		 * @return bool
		 */
		public function sequenceEqual(array $otherCollection, EqualityComparer $comparer = null) {
			$collection = $this->toArray();
			$count = count($collection);

			if ($count !== count($otherCollection)) {
				return false;
			}

			$comparer = $comparer ?: DefaultEqualityComparer::getInstance();

			for ($i = 0; $i < $count; $i++) {
				if ($comparer->equals($collection[$i], $otherCollection[$i]) !== 0) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Correlates elements of the two collections based on matching keys
		 *
		 * @param array $collectionToJoinOn
		 * @param Closure $innerKeySelector Takes one argument, the element's value, and returns the join key for that object
		 * @param Closure $outerKeySelector Takes one argument, the element's value, and returns the join key for that object
		 * @param Closure $resultSelector Takes two arguments, the matching elements from each collection, and returns a single value
		 * @param EqualityComparer $comparer
		 * @return Phinq
		 */
		public function join(array $collectionToJoinOn, Closure $innerKeySelector, Closure $outerKeySelector, Closure $resultSelector, EqualityComparer $comparer = null) {
			$this->addToQueue(new JoinQuery($collectionToJoinOn, $innerKeySelector, $outerKeySelector, $resultSelector, $comparer));
			return $this;
		}

		/**
		 * Correlates elements into groupings of the two collections based on matching keys
		 *
		 * This is basically an outer join.
		 *
		 * @param array $collectionToJoinOn
		 * @param Closure $innerKeySelector Takes one argument, the element's value, and returns the join key for that object
		 * @param Closure $outerKeySelector Takes one argument, the element's value, and returns the join key for that object
		 * @param Closure $resultSelector Takes two arguments, the matching elements from each collection, and returns a single value
		 * @param EqualityComparer $comparer
		 * @return Phinq
		 */
		public function groupJoin(array $collectionToJoinOn, Closure $innerKeySelector, Closure $outerKeySelector, Closure $resultSelector, EqualityComparer $comparer = null) {
			$this->addToQueue(new GroupJoinQuery($collectionToJoinOn, $innerKeySelector, $outerKeySelector, $resultSelector, $comparer));
			return $this;
		}

		/**
		 * Casts all elements in the collection to the specified type
		 *
		 * Note that isn't particularly useful in PHP, since there is no polymorphism, and
		 * hence casting is not very relevant. But if you want to cast to array, int, string
		 * and so forth, this will do it for you.
		 *
		 * Also note that internally this uses the appropriate cast token (e.g. <kbd>(int)</kbd>)
		 * so if you try to cast stuff you shouldn't be (like an object to an int) then the native
		 * PHP error will bubble up.
		 *
		 * @param string $type One of string, int, float, bool, array, object, binary or null
		 * @return Phinq
		 */
		public function cast($type) {
			$this->addToQueue(new CastQuery($type));
			return $this;
		}

		/**
		 * Filters the collection to only objects of the specified type
		 *
		 * This uses the instanceof operator, so don't try to pass in "string" or
		 * something else that is stupid.
		 *
		 * @param string $type The type to filter for
		 * @return Phinq
		 */
		public function ofType($type) {
			$this->addToQueue(new OfTypeQuery($type));
			return $this;
		}

		/**
		 * Returns the collection if non-empty, or if empty a collection containing the
		 * given default value
		 *
		 * @param mixed $defaultValue
		 * @return Phinq
		 */
		public function defaultIfEmpty($defaultValue = null) {
			$this->addToQueue(new DefaultIfEmptyQuery($defaultValue));
			return $this;
		}

		/**
		 * Merges the given collection into the collection using the given result selector
		 *
		 * @param array $collectionToMerge
		 * @param Closure $resultSelector Takes in two arguments, and returns a single value
		 * @return Phinq
		 */
		public function zip(array $collectionToMerge, Closure $resultSelector) {
			$this->addToQueue(new ZipQuery($collectionToMerge, $resultSelector));
			return $this;
		}

		/**
		 * Applies a lambda function to each element
		 *
		 * This does NOT modify the collection in any way.
		 *
		 * @param Closure $lambda Takes one argument, the current element, with no return value
		 * @return Phinq
		 */
		public function walk(Closure $lambda) {
			$this->addToQueue(new WalkExpression($lambda));
			return $this;
		}

		/**
		 * @ignore
		 */
		public function current() {
			$collection = $this->toArray();
			return $collection[$this->index];
		}

		/**
		 * @ignore
		 */
		public function key() {
			return $this->index;
		}

		/**
		 * @ignore
		 */
		public function next() {
			$this->index++;
		}

		/**
		 * @ignore
		 */
		public function rewind() {
			$this->index = 0;
		}

		/**
		 * @ignore
		 */
		public function valid() {	
			return array_key_exists($this->index, $this->toArray());
		}
	}

?>