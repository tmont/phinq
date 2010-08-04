<?php

	namespace Phinq\Tests;

	use Phinq\Phinq;
	use stdClass;

	class CoreTests extends \PHPUnit_Framework_TestCase {

		public function testBasicFilter() {
			$collection = array(1, 2, 3, 4, 5, 6);
			$filteredCollection = Phinq::create($collection)
				->where(function($value) { return $value % 2 === 0; })
				->toArray();

			self::assertSame(array(2, 4, 6), $filteredCollection);
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

			self::assertSame(array(1, 3, 5), $orderedCollection);
		}

		public function testOrderByDescending() {
			$collection = array(5, 1, 3);
			$orderedCollection = Phinq::create($collection)
				->orderBy(function($value) { return $value; }, true)
				->toArray();

			self::assertSame(array(5, 3, 1), $orderedCollection);
		}

		public function testUnion() {
			$collection1 = array(1, 2, 3);
			$collection2 = array(2, 3, 4);
			$unionedCollection = Phinq::create($collection1)
				->union($collection2)
				->toArray();

			self::assertSame(array(1, 2, 3, 4), $unionedCollection);
		}

		public function testUnionWithComparer() {
			$collection1 = array(new Sphinqter('foo', 'obj1'), new Sphinqter('bar', 'obj1'));
			$collection2 = array(new Sphinqter('foo', 'obj2'), new Sphinqter('baz', 'obj2'));
			$unionedCollection = Phinq::create($collection1)
				->union($collection2, new IdComparer())
				->toArray();

			self::assertEquals(3, count($unionedCollection));
			self::assertEquals($unionedCollection[0]->id, 'foo');
			self::assertEquals($unionedCollection[1]->id, 'bar');
			self::assertEquals($unionedCollection[2]->id, 'baz');
		}

		public function testConcat() {
			$collection1 = array(1, 2, 3);
			$collection2 = array(2, 3, 4);
			$concatedCollection = Phinq::create($collection1)
				->concat($collection2)
				->toArray();

			self::assertSame(array(1, 2, 3, 2, 3, 4), $concatedCollection);
		}

		public function testConcatWithObjects() {
			$foo1 = new Sphinqter('foo');
			$bar = new Sphinqter('bar');
			$baz = new Sphinqter('baz');
			$foo2 = new Sphinqter('foo');

			$collection1 = array($foo1, $bar);
			$collection2 = array($baz, $foo2);

			$concatedCollection = Phinq::create($collection1)
				->concat($collection2)
				->toArray();

			self::assertSame(array($foo1, $bar, $baz, $foo2), $concatedCollection);
		}

		public function testIntersect() {
			$collection1 = array(1, 2, 3);
			$collection2 = array(2, 3, 4);
			$intersectedCollection = Phinq::create($collection1)
				->intersect($collection2)
				->toArray();

			self::assertSame(array(2, 3), $intersectedCollection);
		}

		public function testIntersectWithComparer() {
			$foo = new Sphinqter('foo');
			$bar = new Sphinqter('bar');
			$baz = new Sphinqter('baz');

			$collection1 = array($foo, $bar);
			$collection2 = array($baz, $foo);

			$intersectedCollection = Phinq::create($collection1)
				->intersect($collection2)
				->toArray();

			self::assertSame(array($foo), $intersectedCollection);
		}

		public function testDistinct() {
			$collection = array(1, 2, 3, 1, 2, 4);
			$newCollection = Phinq::create($collection)
				->distinct()
				->toArray();

			self::assertSame(array(1, 2, 3, 4), $newCollection);
		}

		public function testDistinctWithComparer() {
			$foo = new Sphinqter('foo');
			$bar = new Sphinqter('bar');
			$baz = new Sphinqter('baz');

			$collection = array($foo, $bar, $foo, $bar, $baz, $foo, $foo);
			$newCollection = Phinq::create($collection)
				->distinct(new IdComparer())
				->toArray();

			self::assertSame(array($foo, $bar, $baz), $newCollection);
		}

		public function testSkip() {
			$collection = array(1, 2, 3, 4, 5, 6);
			$newCollection = Phinq::create($collection)
				->skip(2)
				->toArray();

			self::assertSame(array(3, 4, 5, 6), $newCollection);
		}

		public function testSkipNegative() {
			$collection = array(1, 2, 3, 4, 5, 6);
			$newCollection = Phinq::create($collection)
				->skip(-2)
				->toArray();

			self::assertSame(array(4, 5, 6), $newCollection);
		}

		public function testSkipOutOfBounds() {
			$collection = array(1, 2, 3, 4, 5, 6);
			$newCollection = Phinq::create($collection)
				->skip(10)
				->toArray();

			self::assertSame(array(), $newCollection);

			$newCollection = Phinq::create($collection)
				->skip(-10)
				->toArray();

			self::assertSame(array(1, 2, 3, 4, 5, 6), $newCollection);
		}

		public function testSkipZeroReturnsOriginalCollection() {
			$collection = array(1, 2, 3, 4, 5, 6);
			$newCollection = Phinq::create($collection)
				->skip(0)
				->toArray();

			self::assertSame($collection, $newCollection);
		}

		public function testSkipWhile() {
			$collection = range(1, 6);
			$newCollection = Phinq::create($collection)
				->skipWhile(function($value) { return $value < 3; })
				->toArray();

			self::assertSame(array(3, 4, 5, 6), $newCollection);
		}

		public function testTake() {
			$collection = range(1, 6);
			$newCollection = Phinq::create($collection)
				->take(2)
				->toArray();

			self::assertSame(array(1, 2), $newCollection);
		}

		public function testTakeWithNegative() {
			$this->setExpectedException('OutOfBoundsException');
			$collection = range(1, 6);
			Phinq::create($collection)
				->take(-2)
				->toArray();
		}

		public function testTakeZeroReturnsEmptyArray() {
			$collection = range(1, 6);
			$newCollection = Phinq::create($collection)
				->take(0)
				->toArray();

			self::assertSame(array(), $newCollection);
		}

		public function testTakeWhile() {
			$collection = range(1, 6);
			$newCollection = Phinq::create($collection)
				->takeWhile(function($value) { return $value < 3; })
				->toArray();

			self::assertSame(array(1, 2), $newCollection);
		}

		public function testAll() {
			$collection = range(1, 6);
			self::assertTrue(Phinq::create($collection)->all(function($value) { return is_int($value); }));
			self::assertFalse(Phinq::create($collection)->all(function($value) { return $value < 6; }));
		}

		public function testAllWithEmptyCollection() {
			self::assertTrue(Phinq::create(array())->all(function($value) { return true; }));
			self::assertTrue(Phinq::create(array())->all(function($value) { return false; }));
		}

		public function testAnyWithoutPredicate() {
			self::assertTrue(Phinq::create(range(1, 6))->any());
			self::assertFalse(Phinq::create(array())->any());
		}

		public function testAnyWithPredicate() {
			self::assertTrue(Phinq::create(range(1, 6))->any(function($value) { return $value === 3; }));
			self::assertFalse(Phinq::create(array())->any(function($value) { return $value !== null; }));
		}

		public function testContainsAndDefaultEqualityComparer() {
			$obj = new stdClass();
			$resource = xml_parser_create();
			$collection = array(1, 2, 'foo', $obj, $resource, array('bar'), null);
			
			self::assertTrue(Phinq::create($collection)->contains(1));
			self::assertTrue(Phinq::create($collection)->contains(2.0));
			self::assertTrue(Phinq::create($collection)->contains('foo'));
			self::assertTrue(Phinq::create($collection)->contains($obj));
			self::assertTrue(Phinq::create($collection)->contains($resource));
			self::assertTrue(Phinq::create($collection)->contains(array('bar')));
			self::assertTrue(Phinq::create($collection)->contains(null));

			self::assertFalse(Phinq::create($collection)->contains(new stdClass()));
			self::assertFalse(Phinq::create($collection)->contains('2'));
			self::assertFalse(Phinq::create($collection)->contains(xml_parser_create()));
			self::assertFalse(Phinq::create($collection)->contains(array(1 => 'bar')));
		}

		public function testContainsWithComparer() {
			$obj1 = new Sphinqter('foo');
			$obj2 = new Sphinqter('bar');
			$obj3 = new Sphinqter('baz');
			
			$collection = array($obj1, $obj2, $obj3);
			$comparer = new IdComparer();

			self::assertTrue(Phinq::create($collection)->contains($obj1, $comparer));
			self::assertTrue(Phinq::create($collection)->contains($obj2, $comparer));
			self::assertTrue(Phinq::create($collection)->contains($obj3, $comparer));
			self::assertTrue(Phinq::create($collection)->contains(new Sphinqter('foo'), $comparer));
			self::assertTrue(Phinq::create($collection)->contains(new Sphinqter('bar'), $comparer));
			self::assertTrue(Phinq::create($collection)->contains(new Sphinqter('baz'), $comparer));
		}

		public function testCount() {
			self::assertSame(3, Phinq::create(array(1, 2, 3))->count());
			self::assertSame(0, Phinq::create(array())->count());
		}

		public function testCountWithPredicate() {
			self::assertSame(2, Phinq::create(array(1, 2, 3))->count(function($value) { return $value < 3; }));
			self::assertSame(0, Phinq::create(array(1, 2, 3))->count(function($value) { return false; }));
			self::assertSame(3, Phinq::create(array(1, 2, 3))->count(function($value) { return true; }));
		}

		public function testReverse() {
			$collection = Phinq::create(array(1, 2, 3))->reverse()->toArray();
			
			self::assertSame(array(3, 2, 1), $collection);
			self::assertSame(array(), Phinq::create(array())->reverse()->toArray());
		}

	}
	
?>