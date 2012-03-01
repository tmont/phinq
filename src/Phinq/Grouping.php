<?php

namespace Phinq;

class Grouping extends Phinq
{
	/**
	 * 
	 * @var unknown_type
	 */
	protected $key;

	/**
	 * 
	 * @param array $collection
	 * @param unknown_type $key
	 */
	public function __construct(array $collection, $key)
	{
		parent::__construct($collection);
		$this->key = $key;
	}

	/**
	 * @return unknown_type
	 */
	public final function getKey()
	{
		return $this->key;
	}

}