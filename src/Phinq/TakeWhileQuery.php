<?php

	namespace Phinq;

	class TakeWhileQuery extends LambdaDrivenQuery {

		public function execute(array $collection) {
			$sliceIndex = -1;
			$predicate = $this->getLambda();
			while (array_key_exists(++$sliceIndex, $collection) && $predicate($collection[$sliceIndex]));

			return array_slice($collection, 0, $sliceIndex);
		}
	}

?>