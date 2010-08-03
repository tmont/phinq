<?php

	namespace Phinq;

	class ConcatQuery implements Query {

		private $additionalCollection = array();

		public function __construct(array $additionalCollection) {
			$this->additionalCollection = array_values($additionalCollection);
		}

		public function execute(array $collection) {
			return array_merge($collection, $this->additionalCollection);
		}
	}
	
?>