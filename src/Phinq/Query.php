<?php

namespace Phinq;

interface Query
{
	/**
	 * Executes the query with the given collection as input.
	 * @param array $collection The collection to operate on
	 * @return array The new collection
	 */
	function execute(array $collection);
}