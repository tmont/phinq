<?php

	namespace Phinq;

	use Traversable, InvalidArgumentException;

	final class Util {

		//@codeCoverageIgnoreStart
		private function __construct() {}
		//@codeCoverageIgnoreEnd

		public static function nonRecursiveFlatten(array $array) {
			$flattened = array();
			foreach ($array as $value) {
				$flattened[] = $value;
			}

			return $flattened;
		}

		public static function compare($a, $b) {
			if (is_int($a) || is_float($a)) {
				return (is_int($b) || is_float($b)) && $a == $b ? 0 : ($a < $b ? -1 : 1);
			} else if ($a === null) {
				return $b === null ? 0 : ($a < $b ? -1 : 1);
			} else if (is_string($a)) {
				return is_string($b) && $a === $b ? 0 : ($a < $b ? -1 : 1);
			} else if (is_object($a)) {
				return is_object($b) && $a === $b ? 0 : 1; //reference equals
			} else if (is_array($a)) {
				return is_array($b) && $a === $b ? 0 : ($a < $b ? -1 : 1); //keys must match
			} else if (is_resource($a)) {
				return is_resource($b) && $a === $b ? 0 : ($a < $b ? -1 : 1);
			
			//@codeCoverageIgnoreStart
			} else {
				//this is just a safeguard in case PHP adds more types or something
				return $a === $b ? 0 : ($a < $b ? -1 : 1);
			}
			//@codeCoverageIgnoreEnd
		}

		public static function convertToNumericallyIndexedArray($collection) {
			if (is_array($collection)) {
				return array_values($collection);
			} else if ($collection instanceof Phinq) {
				return $collection->toArray();
			} else if ($collection instanceof Traversable) {
				$array = array();
				foreach ($collection as $value) {
					$array[] = $value;
				}
				return $array;
			}

			throw new InvalidArgumentException('Unable to convert value to an array');
		}

	}
	
?>