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

		/**
		 * @todo Implement the negative part of each branch
		 */
		public function equals($a, $b) {
			if (is_int($a) || is_float($a)) {
				return (is_int($b) || is_float($b)) && $a == $b ? 0 : ($a < $b ? -1 : 1);
			} else if ($a === null) {
				return $b === null ? 0 : 1;
			} else if (is_string($a)) {
				return is_string($b) && $a === $b ? 0 : 1;
			} else if (is_object($a)) {
				return is_object($b) && $a === $b ? 0 : 1; //reference equals
			} else if (is_array($a)) {
				return is_array($b) && $a === $b ? 0 : 1; //keys must match
			} else if (is_resource($a)) {
				return is_resource($b) && $a === $b ? 0 : 1;
			} else {
				return $a === $b ? 0 : 1;
			}
		}
	}
	
?>