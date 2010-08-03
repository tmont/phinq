<?php

	namespace Phinq\Tests;

	use Phinq\Phinq;

	class RetrievalTests extends \PHPUnit_Framework_TestCase {

		public function testFirst() {
			self::assertSame(1, Phinq::create(array(1, 2, 3, 4, 5, 6))->first());
		}

		public function testFirstWithNoElements() {
			$this->setExpectedException('Phinq\EmptyCollectionException');
			Phinq::create(array())->first();
		}

		public function testFirstWithFilter() {
			self::assertSame(4, Phinq::create(array(1, 2, 3, 4, 5, 6))->first(function($value) { return $value > 3; }));
		}

		public function testFirstOrDefault() {
			self::assertSame(1, Phinq::create(array(1, 2, 3, 4, 5, 6))->firstOrDefault());
		}

		public function testFirstOrDefaultWithFilter() {
			self::assertSame(4, Phinq::create(array(1, 2, 3, 4, 5, 6))->firstOrDefault(function($value) { return $value > 3; }));
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
			$this->setExpectedException('BadMethodCallException');
			Phinq::create(array())->single();
		}

		public function testSingleWithMoreThanOneElement() {
			$this->setExpectedException('BadMethodCallException');
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
			$this->setExpectedException('BadMethodCallException');
			Phinq::create(array(1, 2))->singleOrDefault();
		}

		public function testLast() {
			self::assertSame(6, Phinq::create(array(1, 2, 3, 4, 5, 6))->last());
		}

		public function testLastWithNoElements() {
			$this->setExpectedException('Phinq\EmptyCollectionException');
			Phinq::create(array())->last();
		}

		public function testLastWithFilter() {
			self::assertSame(2, Phinq::create(array(1, 2, 3, 4, 5, 6))->last(function($value) { return $value < 3; }));
		}

		public function testLastOrDefault() {
			self::assertSame(6, Phinq::create(array(1, 2, 3, 4, 5, 6))->lastOrDefault());
		}

		public function testLastOrDefaultWithFilter() {
			self::assertSame(2, Phinq::create(array(1, 2, 3, 4, 5, 6))->lastOrDefault(function($value) { return $value < 3; }));
		}

		public function testLastOrDefaultWithNoElements() {
			self::assertNull(Phinq::create(array())->lastOrDefault());
		}

		public function testElementAt() {
			self::assertSame(1, Phinq::create(array(1, 2, 3, 4, 5, 6))->elementAt(0));
			self::assertSame(4, Phinq::create(array(1, 2, 3, 4, 5, 6))->elementAt(3));
			self::assertSame(6, Phinq::create(array(1, 2, 3, 4, 5, 6))->elementAt(5));
		}

		public function testElementAtWithNegativeIndex() {
			self::assertSame(6, Phinq::create(array(1, 2, 3, 4, 5, 6))->elementAt(-1));
			self::assertSame(4, Phinq::create(array(1, 2, 3, 4, 5, 6))->elementAt(-3));
			self::assertSame(1, Phinq::create(array(1, 2, 3, 4, 5, 6))->elementAt(-6));
		}

		public function testElementAtWithNegativeIndexBeyondLength() {
			$this->setExpectedException('OutOfBoundsException', 'Collection does not contain an element at index -10');
			Phinq::create(array(1, 2, 3, 4, 5, 6))->elementAt(-10);
		}

		public function testElementAtWithIndexBeyondLength() {
			$this->setExpectedException('OutOfBoundsException');
			Phinq::create(array(1, 2, 3, 4, 5, 6))->elementAt(6);
		}

		public function testElementAtWithEmptyCollection() {
			$this->setExpectedException('OutOfBoundsException');
			Phinq::create(array())->elementAt(0);
		}

		public function testElementAtWithNonIntegralIndex() {
			$this->setExpectedException('InvalidArgumentException');
			Phinq::create(array())->elementAt('foo');
		}

		public function testElementAtOrDefault() {
			self::assertSame(1, Phinq::create(array(1, 2, 3, 4, 5, 6))->elementAtOrDefault(0));
			self::assertSame(4, Phinq::create(array(1, 2, 3, 4, 5, 6))->elementAtOrDefault(3));
			self::assertSame(6, Phinq::create(array(1, 2, 3, 4, 5, 6))->elementAtOrDefault(5));
			
			self::assertNull(Phinq::create(array(1, 2, 3, 4, 5, 6))->elementAtOrDefault(7));
		}

		public function testelementAtOrDefaultOrDefaultWithNegative() {
			self::assertSame(6, Phinq::create(array(1, 2, 3, 4, 5, 6))->elementAtOrDefault(-1));
			self::assertSame(4, Phinq::create(array(1, 2, 3, 4, 5, 6))->elementAtOrDefault(-3));
			self::assertSame(1, Phinq::create(array(1, 2, 3, 4, 5, 6))->elementAtOrDefault(-6));

			self::assertNull(Phinq::create(array(1, 2, 3, 4, 5, 6))->elementAtOrDefault(-10));
		}

	}

?>