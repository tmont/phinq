<?php

	namespace Phinq;

	use Closure;

	class GroupByQuery extends LambdaDrivenQuery {

		public function execute(array $collection) {
			$lambda = $this->getLambda();
			$dictionary = new ComplexKeyDictionary();

			$this->walk($collection, function($key, $value) use (&$dictionary, $lambda) {
				$dictionary[$lambda($value, $key)] = $value;
			});

			$groupings = array();
			foreach ($dictionary as $grouping) {
				$groupings[] = new Grouping($grouping['values'], $grouping['key']);
			}

			return $groupings;
		}

	}

?>
