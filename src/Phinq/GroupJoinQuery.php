<?php

namespace Phinq;

use Closure;

class GroupJoinQuery extends ComparableQuery
{

	protected $collectionToJoinOn;
	protected $innerKeySelector;
	protected $outerKeySelector;
	protected $resultSelector;
	protected $comparer;

	public function __construct(array $collectionToJoinOn, Closure $innerKeySelector, Closure $outerKeySelector, Closure $resultSelector, EqualityComparer $comparer = null)
	{
		parent::__construct($comparer);
		
		$this->collectionToJoinOn = $collectionToJoinOn;
		$this->innerKeySelector = $innerKeySelector;
		$this->outerKeySelector = $outerKeySelector;
		$this->resultSelector = $resultSelector;
	}

	public function execute(array $collection)
	{
		$innerKeySelector = $this->innerKeySelector;
		$outerKeySelector = $this->outerKeySelector;
		$comparer         = $this->getComparer();
		$outerCount       = count($this->collectionToJoinOn);
		$outerCollection  = $this->collectionToJoinOn;
		$dictionary       = new GroupingDictionary();

		array_walk(
			$collection,
			function($value, $key) use ($innerKeySelector, $outerKeySelector, $comparer, $outerCount, $outerCollection, &$dictionary) {
				$innerKey = $innerKeySelector($value);
				for ($i = 0; $i < $outerCount; $i++) {
					if ($comparer->equals($innerKey, $outerKeySelector($outerCollection[$i])) === 0) {
						$dictionary[$value] = $outerCollection[$i];
					}
				}
			}
		);

		//there's probably a more efficient way to do this... but does anybody ever actually use GroupJoin()? I mean, come on!
		$resultSelector = $this->resultSelector;
		$newCollection = array();
		foreach ($collection as $value)
		{
			if (isset($dictionary[$value]))
			{
				$newCollection[] = $resultSelector($value, $dictionary[$value]);
			}
			else
			{
				$newCollection[] = $resultSelector($value, array());
			}
		}

		return $newCollection;
	}
}