<?php

	namespace Phinq;

	class DefaultEqualityComparer implements EqualityComparer {

		/**
		 * @todo Implement the negative part of each branch
		 */
		public function equals($a, $b) {
			if (is_numeric($a)) {
				return is_numeric($b) && $a == $b ? 0 : ($a < $b ? -1 : 1); //types don't have to match
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