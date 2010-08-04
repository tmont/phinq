<?php

	namespace Phinq;

	class DistinctQuery implements Query {

		private $comparer;

		public function __construct(EqualityComparer $comparer = null) {
			$this->comparer = $comparer ?: DefaultEqualityComparer::getInstance();
		}

		public function execute(array $collection) {
			//this is pretty lame, but you can't pass a user-defined function to array_unique
			//spl_object_hash() could have been useful, but I wanted to make sure phinq works with arrays containing many different types, not just objects
			for ($i = 0, $count = count($collection); $i < $count; $i++) {
				$indexesToUnset = array();
				for ($j = $i + 1; $j < $count; $j++) {
					if ($this->comparer->equals($collection[$i], $collection[$j]) === 0) {
						$indexesToUnset[] = $j;
					}
				}

				if (!empty($indexesToUnset)) {
					foreach ($indexesToUnset as $index) {
						unset($collection[$index]);
						$count--;
					}

					$collection = array_values($collection); //need to reorder indexes after unset()
				}
			}

			return $collection;
		}
	}

?>