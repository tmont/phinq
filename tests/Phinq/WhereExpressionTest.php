<?php

	namespace Phinq\Tests;

	use Phinq\Phinq;

	class WhereExpressionTest extends \PHPUnit_Framework_TestCase {

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
			$collection1 = array($foo1, $bar);

			$foo2 = new Sphinqter('foo');
			$baz = new Sphinqter('baz');

			$collection2 = array($baz, $foo2);
			$concatedCollection = Phinq::create($collection1)
				->concat($collection2)
				->toArray();

			self::assertSame(array($foo1, $bar, $baz, $foo2), $concatedCollection);
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