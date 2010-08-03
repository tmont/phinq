<?php

	namespace Phinq\Tests;

	use Phinq\Phinq;

	class PhinqTest extends \PHPUnit_Framework_TestCase {

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

		public function testTake() {
			$collection = array(1, 2, 3, 4, 5, 6);
			$newCollection = Phinq::create($collection)
				->take(2)
				->toArray();

			self::assertSame(array(1, 2), $newCollection);
		}

		public function testTakeWithNegative() {
			$this->setExpectedException('OutOfBoundsException');
			$collection = array(1, 2, 3, 4, 5, 6);
			Phinq::create($collection)
				->take(-2)
				->toArray();
		}

		public function testTakeZeroReturnsEmptyArray() {
			$collection = array(1, 2, 3, 4, 5, 6);
			$newCollection = Phinq::create($collection)
				->take(0)
				->toArray();

			self::assertSame(array(), $newCollection);
		}

		public function testFirst() {
			self::assertSame(1, Phinq::create(array(1, 2, 3, 4, 5, 6))->first());
		}

		public function testFirstWithNoElements() {
			$this->setExpectedException('OutOfBoundsException');
			Phinq::create(array())->first();
		}

		public function testFirstWithFilter() {
			self::assertSame(4, Phinq::create(array(1, 2, 3, 4, 5, 6))->first(function($value) { return $value > 3; }));
		}

		public function testFirstOrDefault() {
			self::assertSame(1, Phinq::create(array(1, 2, 3, 4, 5, 6))->firstOrDefault());
		}

		public function testFirstOrDefaultWithFilter() {
			self::assertSame(4, Phinq::create(array(1, 2, 3, 4, 5, 6))->first(function($value) { return $value > 3; }));
		}

		public function testFirstOrDefaultWithNoElements() {
			self::assertNull(Phinq::create(array())->firstOrDefault());
		}

		public function testSingle() {
			self::assertSame(1, Phinq::create(array(1))->single());
		}

		public function testSingleWithFilter() {
			self::assertSame(6, Phinq::create(array(1, 2, 3, 4, 5, 6))->single(function($value) { return $value > 5; }));
		}

		public function testSingleWithNoElements() {
			$this->setExpectedException('RuntimeException');
			Phinq::create(array())->single();
		}

		public function testSingleWithMoreThanOneElement() {
			$this->setExpectedException('RuntimeException');
			Phinq::create(array(1, 2))->single();
		}

		public function testSingleOrDefault() {
			self::assertSame(1, Phinq::create(array(1))->singleOrDefault());
		}

		public function testSingleOrDefaultWithFilter() {
			self::assertSame(6, Phinq::create(array(1, 2, 3, 4, 5, 6))->singleOrDefault(function($value) { return $value > 5; }));
		}

		public function testSingleOrDefaultWithNoElements() {
			self::assertNull(Phinq::create(array())->singleOrDefault());
		}

		public function testSingleOrDefaultWithMoreThanOneElement() {
			$this->setExpectedException('RuntimeException');
			Phinq::create(array(1, 2))->singleOrDefault();
		}
	}

	class IdComparer implements \Phinq\EqualityComparer {
		public function equals($a, $b) {
			return $a->id === $b->id ? 0 : 1;
		}
	}

	class Sphinqter {
		public $id;
		public $foo;

		public function __construct($id = null, $foo = null) {
			$this->id = $id;
			$this->foo = $foo;
		}
	}
	
?>