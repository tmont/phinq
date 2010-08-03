<?php

	namespace Phinq;

	class IntersectQuery implements Query {

		private $collectionToIntersect;
		private $comparer;

		public function __construct(array $collectionToIntersect, EqualityComparer $comparer = null) {
			$this->collectionToIntersect = array_values($collectionToIntersect);
			$this->comparer = $comparer ?: DefaultEqualityComparer::getInstance();
		}

		public function execute(array $collection) {
			$comparer = $this->comparer;
			return array_values(array_uintersect($collection, $this->collectionToIntersect, function($a, $b) use ($comparer) { return $comparer->equals($a, $b); }));
		}
	}

?>