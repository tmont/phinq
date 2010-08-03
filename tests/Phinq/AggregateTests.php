<?php

	namespace Phinq\Tests;

	use Phinq\Phinq;

	class AggregateTests extends \PHPUnit_Framework_TestCase {

		public function testGroupBy() {
			$collection = array('foo', 'bar', 'baz', 'bat');
			$groupings = Phinq::create($collection)
				->groupBy(function($value) { return $value[0]; })
				->toArray();

			self::assertEquals(2, count($groupings));
			self::assertEquals('f', $groupings[0]->getKey());
			self::assertEquals('b', $groupings[1]->getKey());

			$stringsThatStartWithF = $groupings[0]->toArray();
			$stringsThatStartWithB = $groupings[1]->toArray();

			self::assertEquals(1, count($stringsThatStartWithF));
			self::assertEquals('foo', $stringsThatStartWithF[0]);

			self::assertEquals(3, count($stringsThatStartWithB));
			self::assertEquals('bar', $stringsThatStartWithB[0]);
			self::assertEquals('baz', $stringsThatStartWithB[1]);
			self::assertEquals('bat', $stringsThatStartWithB[2]);
		}

	}
	
?>