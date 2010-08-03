<?php

	namespace Phinq\Tests;

	use Phinq\Phinq;

	class AggregateTests extends \PHPUnit_Framework_TestCase {

		public function testGroupBy() {
			$collection = array('foo', 'bar', 'baz', 'bat');
			$groupings = Phinq::create($collection)
				->groupBy(function($value) { return $value[0]; })
				->toArray();

			self::assertSame(2, count($groupings));
			self::assertSame('f', $groupings[0]->getKey());
			self::assertSame('b', $groupings[1]->getKey());

			$stringsThatStartWithF = $groupings[0]->toArray();
			$stringsThatStartWithB = $groupings[1]->toArray();

			self::assertSame(1, count($stringsThatStartWithF));
			self::assertSame('foo', $stringsThatStartWithF[0]);

			self::assertSame(3, count($stringsThatStartWithB));
			self::assertSame('bar', $stringsThatStartWithB[0]);
			self::assertSame('baz', $stringsThatStartWithB[1]);
			self::assertSame('bat', $stringsThatStartWithB[2]);
		}

		public function testGroupByWithObjectAsKey() {
			$id1 = new Sphinqter('foo');
			$id2 = new Sphinqter('foo');
			
			$foo = new Sphinqter($id1);
			$bar = new Sphinqter($id2);
			$baz = new Sphinqter($id1);
			
			$collection = array($foo, $bar, $baz);
			$groupings = Phinq::create($collection)
				->groupBy(function($value) { return $value->id; })
				->toArray();

			self::assertSame(2, count($groupings));
			self::assertSame($id1, $groupings[0]->getKey());
			self::assertSame($id2, $groupings[1]->getKey());

			$sphinqtersWithId1 = $groupings[0]->toArray();
			$sphinqtersWithId2 = $groupings[1]->toArray();

			self::assertSame(2, count($sphinqtersWithId1));
			self::assertSame($foo, $sphinqtersWithId1[0]);
			self::assertSame($baz, $sphinqtersWithId1[1]);

			self::assertSame(1, count($sphinqtersWithId2));
			self::assertSame($bar, $sphinqtersWithId2[0]);
		}

		public function testMax() {
			$collection = array(2, 4, 3, 6, 5, 1);
			self::assertEquals(6, Phinq::create($collection)->max());
			self::assertEquals(3, Phinq::create($collection)->max(function($value) { return $value > 3 ? 0 : $value; }));
		}

		public function testMaxWithEmptyCollection() {
			self::assertNull(Phinq::create(array())->max());
		}

		public function testMaxWithNonNumbers() {
			$obj1 = new Sphinqter('foo');
			$obj2 = new Sphinqter('bar');
			$obj3 = new Sphinqter('baz');
			
			$collection = array($obj1, $obj2, $obj3);

			self::assertSame($obj1, Phinq::create($collection)->max());
			self::assertSame($obj1, Phinq::create($collection)->max(function($value) { return $value->id; }));
		}

		public function testMaxWithDuplicateValues() {
			$obj1 = new Sphinqter('foo');
			$obj2 = new Sphinqter('foo');
			$obj3 = new Sphinqter('foo');

			$collection = array($obj1, $obj2, $obj3);

			self::assertSame($obj1, Phinq::create($collection)->max(function($value) { return $value->id; }));
		}

		public function testMin() {
			$collection = array(2, 4, 3, 6, 5, 1);
			self::assertEquals(1, Phinq::create($collection)->min());
			self::assertEquals(6, Phinq::create($collection)->min(function($value) { return $value > 3 ? 0 : $value; }));
		}

		public function testMinWithEmptyCollection() {
			self::assertNull(Phinq::create(array())->min());
		}

		public function testMinWithDuplicateValues() {
			$obj1 = new Sphinqter('foo');
			$obj2 = new Sphinqter('foo');
			$obj3 = new Sphinqter('foo');

			$collection = array($obj1, $obj2, $obj3);
			
			self::assertSame($obj3, Phinq::create($collection)->min(function($value) { return $value->id; }));
		}

		public function testMinWithNonNumbers() {
			$obj1 = new Sphinqter('foo');
			$obj2 = new Sphinqter('bar');
			$obj3 = new Sphinqter('baz');

			$collection = array($obj1, $obj2, $obj3);

			self::assertSame($obj2, Phinq::create($collection)->min());
			self::assertSame($obj2, Phinq::create($collection)->min(function($value) { return $value->id; }));
		}

		public function testAverage() {
			$collection = array(1, 2, 3, 4, 5, 6);
			self::assertEquals(3.5, Phinq::create($collection)->average());
			self::assertEquals(7, Phinq::create($collection)->average(function($value) { return $value * 2; }));
		}

		public function testAverageWithNonNumericValues() {
			$collection = array('foo', 'bar', 'baz');
			self::assertEquals(0, Phinq::create($collection)->average());
		}

		public function testAverageWithEmptyCollection() {
			self::assertEquals(0, Phinq::create(array())->average());
		}

		public function testSum() {
			$collection = array(1, 2, 3, 4, 5, 6);
			self::assertEquals(21, Phinq::create($collection)->sum());
			self::assertEquals(42, Phinq::create($collection)->sum(function($value) { return $value * 2; }));
		}

		public function testSumWithNonNumericValues() {
			$collection = array('foo', 'bar', 'baz');
			self::assertEquals(0, Phinq::create($collection)->sum());
		}

		public function testAggregate() {
			$factorial = Phinq::create(array(1, 2, 3, 4, 5))->aggregate(function($current, $next) { return $current * $next; }, 1);
			self::assertEquals(120, $factorial);
		}

		public function testAggregateWithEmptyCollection() {
			$factorial = Phinq::create(array())->aggregate(function($current, $next) { return $current * $next; }, 1);
			self::assertEquals(1, $factorial);
		}

	}
	
?>