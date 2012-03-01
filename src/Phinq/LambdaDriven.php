<?php

namespace Phinq;

interface LambdaDriven
{
	/**
	 * Gets the lambda expression associated with this object
	 * @return Closure
	 */
	function getLambdaExpression();
}