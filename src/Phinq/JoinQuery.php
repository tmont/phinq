<?php

	namespace Phinq;

	use Closure;

	class JoinQuery extends ComparableQuery {

		private $collectionToJoinOn;
		private $innerKeySelector;
		private $outerKeySelector;
		private $resultSelector;

		public function __construct(array $collectionToJoinOn, Closure $innerKeySelector, Closure $outerKeySelector, Closure $resultSelector, EqualityComparer $comparer = null) {
			parent::__construct($comparer);
			$this->collectionToJoinOn = $collectionToJoinOn;
			$this->innerKeySelector = $innerKeySelector;
			$this->outerKeySelector = $outerKeySelector;
			$this->resultSelector = $resultSelector;
		}

		public function execute(array $collection) {
			$innerKeySelector = $this->innerKeySelector;
			$outerKeySelector = $this->outerKeySelector;
			$resultSelector   = $this->resultSelector;
			$comparer         = $this->getComparer();
			$outerCount       = count($this->collectionToJoinOn);
			$outerCollection  = $this->collectionToJoinOn;
			$newCollection    = array();

			array_walk(
				$collection,
				function($value, $key) use ($innerKeySelector, $outerKeySelector, $resultSelector, $comparer, $outerCount, $outerCollection, &$newCollection) {
					$innerKey = $innerKeySelector($value);
					for ($i = 0; $i < $outerCount; $i++) {
						if ($comparer->equals($innerKey, $outerKeySelector($outerCollection[$i])) === 0) {
							$newCollection[] = $resultSelector($value, $outerCollection[$i]);
						}
					}
				}
			);

			return $newCollection;
		}
	}
	
?>