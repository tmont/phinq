<?php

namespace Phinq;

use Closure;

class ThenByQuery extends OrderedQuery
{
	/**
	 * 
	 * @var OrderedQuery
	 */
	protected $previousQuery;

	/**
	 * Construct a new instance of this object.
	 * @param OrderedQuery $previousQuery
	 * @param Closure $lambda
	 * @param boolean $descending
	 */
	public function __construct(OrderedQuery $previousQuery, Closure $lambda, $descending = false)
	{
		parent::__construct($lambda, $descending);
		$this->previousQuery = $previousQuery;
	}

	/**
	 * 
	 * @link http://www.php.net/manual/en/function.array-multisort.php#90917
	 * @return Closure
	 */
	public function getSortingCallback()
	{
		//need to perform a multisort using callbacks... which PHP can't do natively

		$previousCallback = $this->previousQuery->getSortingCallback();
		$currentCallback = Util::getDefaultSortCallback($this->getLambdaExpression(), $this->isDescending());

		return function($a, $b) use ($previousCallback, $currentCallback) {
			$previousValue = $previousCallback($a, $b);
			if ($previousValue !== 0) {
				return $previousValue;
			}

			return $currentCallback($a, $b);
		};
	}
}