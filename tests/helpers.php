<?php

	namespace Phinq\Tests;

	use Phinq\EqualityComparer;

	class IdComparer implements EqualityComparer {
		public function equals($a, $b) {
			return $a->id === $b->id ? 0 : 1;
		}
	}

	class Sphinqter {
		public $id;
		public $foo;

		public function __construct($id = null, $foo = null) {
			$this->id = $id;
			$this->foo = $foo;
		}
	}


?>