<?php

namespace Phinq;

class ExceptQuery extends ComparableQuery
{
	/**
	 * The collection to except.
	 * @var array
	 */
	protected $collectionToExcept;

	/**
	 * Construct a new instance of this object.
	 * @param array $collectionToExcept
	 * @param EqualityComparer $comparer
	 */
	public function __construct(array $collectionToExcept, EqualityComparer $comparer = null)
	{
		parent::__construct($comparer);
		$this->collectionToExcept = $collectionToExcept;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phinq.Query::execute()
	 */
	public function execute(array $collection)
	{
		$comparer = $this->getComparer();
		return array_values(array_udiff($collection, $this->collectionToExcept, function($a, $b) use ($comparer) { return $comparer->equals($a, $b); }));
	}
}