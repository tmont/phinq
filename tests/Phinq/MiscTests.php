<?php

	namespace Phinq\Tests;

	use Phinq\Phinq;

	class MiscTests extends \PHPUnit_Framework_TestCase {

		public function testAggregate() {
			$factorial = Phinq::create(array(1, 2, 3, 4, 5))->aggregate(function($current, $next) { return $current * $next; }, 1);
			self::assertEquals(120, $factorial);
		}

		public function testAggregateWithEmptyCollection() {
			$factorial = Phinq::create(array())->aggregate(function($current, $next) { return $current * $next; }, 1);
			self::assertEquals(1, $factorial);
		}

		public function testExcept() {
			$collection1 = array(1, 2, 3, 4, 5, 3, 1);
			$collection2 = array(3, 4, 5, 6, 7);

			$diffedCollection = Phinq::create($collection1)->except($collection2)->toArray();

			self::assertSame(array(1, 2, 1), $diffedCollection);
		}

		public function testExceptWithComparer() {
			$obj1 = new Sphinqter('foo');
			$obj2 = new Sphinqter('bar');
			$obj3 = new Sphinqter('baz');
			$obj4 = new Sphinqter('bat');
			$collection1 = array($obj1, $obj2, $obj2, $obj1);
			$collection2 = array($obj1, $obj3, $obj4);

			$diffedCollection = Phinq::create($collection1)->except($collection2, new IdComparer())->toArray();
			self::assertSame(array($obj2, $obj2), $diffedCollection);
		}

	}

?>