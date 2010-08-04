<?php

	namespace Phinq;

	abstract class ComparableQuery implements Query, Comparer {

		private $comparer;

		public function __construct(EqualityComparer $comparer = null) {
			$this->comparer = $comparer ?: DefaultEqualityComparer::getInstance();
		}

		public final function getComparer() {
			return $this->comparer;
		}

	}
	
?>