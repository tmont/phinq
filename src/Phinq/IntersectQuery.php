<?php

	namespace Phinq;

	class IntersectQuery extends ComparableQuery {

		private $collectionToIntersect;

		public function __construct(array $collectionToIntersect, EqualityComparer $comparer = null) {
			parent::__construct($comparer);
			$this->collectionToIntersect = array_values($collectionToIntersect);
		}

		public function execute(array $collection) {
			$comparer = $this->getComparer();
			return array_values(array_uintersect($collection, $this->collectionToIntersect, function($a, $b) use ($comparer) { return $comparer->equals($a, $b); }));
		}
	}

?>