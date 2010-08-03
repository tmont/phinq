<?php

	namespace Phinq;

	class Grouping extends Phinq {

		private $key;

		public function __construct(array $collection, $key) {
			parent::__construct($collection);
			$this->key = $key;
		}

		public final function getKey() {
			return $this->key;
		}

	}
	
?>