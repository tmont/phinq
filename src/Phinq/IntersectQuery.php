<?php

namespace Phinq;

/**
 * 
 */
class IntersectQuery extends ComparableQuery
{
	/**
	 * Values of the intersection collection.
	 * @var array
	 */
	protected $collectionToIntersect;

	/**
	 * Construct a new instance of this object.
	 * @param array $collectionToIntersect
	 * @param EqualityComparer $comparer
	 */
	public function __construct(array $collectionToIntersect, EqualityComparer $comparer = null)
	{
		parent::__construct($comparer);
		$this->collectionToIntersect = array_values($collectionToIntersect);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phinq.Query::execute()
	 */
	public function execute(array $collection)
	{
		$comparer = $this->getComparer();
		return array_values(array_uintersect($collection, $this->collectionToIntersect, function($a, $b) use ($comparer) { return $comparer->equals($a, $b); }));
	}
}