<?php

namespace Phinq;

use Closure;

class JoinQuery extends ComparableQuery
{
	/**
	 * The collection on which to perform the join.
	 * @var array
	 */
	protected $collectionToJoinOn;
	
	/**
	 * A function to determine the inner key to select with.
	 * @var Closure
	 */
	protected $innerKeySelector;
	
	/**
	 * A function to determine the outer key to select with.
	 * @var Closure
	 */
	protected $outerKeySelector;
	
	/**
	 * A function to determine the resulting collection of the join.
	 * @var Closure
	 */
	protected $resultSelector;

	/**
	 * Construct a new instance of this object.
	 * @param array $collectionToJoinOn
	 * @param Closure $innerKeySelector
	 * @param Closure $outerKeySelector
	 * @param Closure $resultSelector
	 * @param EqualityComparer $comparer
	 */
	public function __construct(array $collectionToJoinOn, Closure $innerKeySelector, Closure $outerKeySelector, Closure $resultSelector, EqualityComparer $comparer = null)
	{
		parent::__construct($comparer);
		$this->collectionToJoinOn = $collectionToJoinOn;
		$this->innerKeySelector = $innerKeySelector;
		$this->outerKeySelector = $outerKeySelector;
		$this->resultSelector = $resultSelector;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phinq.Query::execute()
	 */
	public function execute(array $collection)
	{
		$innerKeySelector = $this->innerKeySelector;
		$outerKeySelector = $this->outerKeySelector;
		$resultSelector   = $this->resultSelector;
		$comparer         = $this->getComparer();
		$outerCount       = count($this->collectionToJoinOn);
		$outerCollection  = $this->collectionToJoinOn;
		$newCollection    = array();

		array_walk(
			$collection,
			function($value, $key) use ($innerKeySelector, $outerKeySelector, $resultSelector, $comparer, $outerCount, $outerCollection, &$newCollection) {
				$innerKey = $innerKeySelector($value);
				for ($i = 0; $i < $outerCount; $i++) {
					if ($comparer->equals($innerKey, $outerKeySelector($outerCollection[$i])) === 0) {
						$newCollection[] = $resultSelector($value, $outerCollection[$i]);
					}
				}
			}
		);

		return $newCollection;
	}
}