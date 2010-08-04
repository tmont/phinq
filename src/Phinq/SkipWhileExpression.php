<?php

	namespace Phinq;

	class SkipWhileExpression extends LambdaExpression {

		public function execute(array $collection) {
			$sliceIndex = -1;
			$predicate = $this->getLambda();
			while (array_key_exists(++$sliceIndex, $collection) && $predicate($collection[$sliceIndex])) {
				unset($collection[$sliceIndex]);
			}

			return array_values($collection);
		}
	}

?>