<?php

namespace Phinq;

use Closure;

class OrderByQuery extends OrderedQuery
{
	public function getSortingCallback()
	{
		$lambda = $this->getLambdaExpression();
		return Util::getDefaultSortCallback($lambda, $this->isDescending());
	}
}