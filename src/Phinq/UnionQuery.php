<?php

namespace Phinq;

class UnionQuery extends ComparableQuery
{

	protected $additionalCollection;
	protected $comparer;

	public function __construct(array $additionalCollection, EqualityComparer $comparer = null) {
		parent::__construct($comparer);
		$this->additionalCollection = array_values($additionalCollection);
	}

	public function execute(array $collection) {
		$comparer = $this->getComparer();
		$diffed = array_udiff($this->additionalCollection, $collection, function($a, $b) use ($comparer) { return $comparer->equals($a, $b); });
		return array_merge($collection, $diffed);
	}
}