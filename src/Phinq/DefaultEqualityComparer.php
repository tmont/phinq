<?php

	namespace Phinq;

	final class DefaultEqualityComparer implements EqualityComparer {

		private static $instance = null;

		private function __construct() {}

		public static function getInstance() {
			if (self::$instance === null) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function equals($a, $b) {
			return Util::compare($a, $b);
		}
	}
	
?>