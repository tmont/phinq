<?php

namespace Phinq;

/**
 * Dictionary used for grouping sets of objects
 *
 * Basically, the values are automatically implemented as arrays.
 */
class GroupingDictionary extends Dictionary
{
	/**
	 * (non-PHPdoc)
	 * @see Phinq.Dictionary::createValue()
	 */
	protected function createValue($oldValue, $newValue)
	{
		$oldValue[] = $newValue;
		return $oldValue;
	}
}