<?php

	namespace Phinq;

	class ExceptQuery implements Query {

		private $comparer;
		private $collectionToExcept;

		public function __construct(array $collectionToExcept, EqualityComparer $comparer = null) {
			$this->comparer = $comparer ?: DefaultEqualityComparer::getInstance();
			$this->collectionToExcept = $collectionToExcept;
		}

		public function execute(array $collection) {
			$comparer = $this->comparer;
			return array_values(array_udiff($collection, $this->collectionToExcept, function($a, $b) use ($comparer) { return $comparer->equals($a, $b); }));
		}
	}
	
?>