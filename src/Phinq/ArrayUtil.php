<?php

	namespace Phinq;

	final class ArrayUtil {

		//@codeCoverageIgnoreStart
		private function __construct() {}
		//@codeCoverageIgnoreEnd

		public static function nonRecursiveFlatten($array) {
			$flattened = array();
			if (is_array($array)) {
				foreach ($array as $value) {
					$flattened[] = $value;
				}
			} else {
				$flattened[] = $array;
			}

			return $flattened;
		}

	}
	
?>