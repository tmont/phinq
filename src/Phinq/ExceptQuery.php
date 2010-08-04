<?php

	namespace Phinq;

	class ExceptQuery extends ComparableQuery {

		private $collectionToExcept;

		public function __construct(array $collectionToExcept, EqualityComparer $comparer = null) {
			parent::__construct($comparer);
			$this->collectionToExcept = $collectionToExcept;
		}

		public function execute(array $collection) {
			$comparer = $this->getComparer();
			return array_values(array_udiff($collection, $this->collectionToExcept, function($a, $b) use ($comparer) { return $comparer->equals($a, $b); }));
		}
	}
	
?>