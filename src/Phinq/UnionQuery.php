<?php

	namespace Phinq;

	class UnionQuery implements Query {

		private $additionalCollection;
		private $comparer;

		public function __construct(array $additionalCollection, EqualityComparer $comparer = null) {
			$this->additionalCollection = array_values($additionalCollection);
			$this->comparer = $comparer ?: DefaultEqualityComparer::getInstance();
		}

		public function execute(array $collection) {
			$comparer = $this->comparer;
			$diffed = array_udiff($this->additionalCollection, $collection, function($a, $b) use ($comparer) { return $comparer->equals($a, $b); });
			return array_merge($collection, $diffed);
		}
	}
	
?>