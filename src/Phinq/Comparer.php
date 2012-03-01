<?php

namespace Phinq;

/**
 * Represents an object that is able to compare objects using an instance
 * of an {@link EqualityComparer}
 */
interface Comparer
{
	/**
	 * Gets the equality comparer to be used by this object
	 *
	 * @return EqualityComparer
	 */
	function getComparer();
}