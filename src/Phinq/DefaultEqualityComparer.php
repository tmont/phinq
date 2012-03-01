<?php

namespace Phinq;

final class DefaultEqualityComparer implements EqualityComparer
{
	protected static $instance = null;

	protected function __construct() {}

	/**
	 * 
	 */
	public static function getInstance()
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phinq.EqualityComparer::equals()
	 */
	public function equals($a, $b)
	{
		return Util::compare($a, $b);
	}
}