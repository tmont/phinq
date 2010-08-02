<?php

	namespace Phinq\Tests;

	use Phinq\Phinq;

	class WhereExpressionTest extends \PHPUnit_Framework_TestCase {

		public function testBasicFilter() {
			$collection = array(1, 2, 3, 4, 5, 6);
			$filteredCollection = Phinq::create($collection)
				->where(function($value) { return $value % 2 === 0; })
				->toArray();

			self::assertSame(array(1 => 2, 3 => 4, 5 => 6), $filteredCollection);
		}

		public function testBasicSelect() {
			$collection = array(1, 2, 3, 4, 5, 6);
			$newCollection = Phinq::create($collection)
				->select(function($value) { return $value * 2; })
				->toArray();

			self::assertSame(array(2, 4, 6, 8, 10, 12), $newCollection);
		}

		public function testOrderBy() {
			$collection = array(5, 1, 3);
			$orderedCollection = Phinq::create($collection)
				->orderBy(function($value) { return $value; })
				->toArray();

			self::assertSame(array(1 => 1, 2 => 3, 0 => 5), $orderedCollection);
		}

		public function testOrderByDescending() {
			$collection = array(5, 1, 3);
			$orderedCollection = Phinq::create($collection)
				->orderBy(function($value) { return $value; }, true)
				->toArray();

			self::assertSame(array(1 => 1, 2 => 3, 0 => 5), $orderedCollection);
		}

	}
	
?>
