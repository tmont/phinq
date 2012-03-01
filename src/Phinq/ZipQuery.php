<?php

namespace Phinq;

use Closure;

class ZipQuery implements Query
{
	protected $collectionToMerge;
	protected $resultSelector;

	public function __construct(array $collectionToMerge, Closure $resultSelector)
	{
		$this->collectionToMerge = $collectionToMerge;
		$this->resultSelector = $resultSelector;
	}

	public function execute(array $collection)
	{
		$resultSelector = $this->resultSelector;
		
		for ($i = 0, $count = min(count($collection), count($this->collectionToMerge)); $i < $count; $i++) {
			$collection[$i] = $resultSelector($collection[$i], $this->collectionToMerge[$i]);
		}

		return array_slice($collection, 0, $i);
	}
}