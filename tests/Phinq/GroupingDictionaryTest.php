<?php

	namespace Phinq\Tests;

	use Phinq\GroupingDictionary;

	class GroupingDictionaryTest extends \PHPUnit_Framework_TestCase {

		public function testArrayAccess() {
			$dictionary = new GroupingDictionary();
			$key = new Sphinqter('foo');
			$dictionary[$key] = 'foo';
			$dictionary[$key] = 'bar';

			self::assertTrue(isset($dictionary[$key]));
			self::assertEquals(1, count($dictionary));
			self::assertSame(array('foo', 'bar'), $dictionary[$key]);
			unset($dictionary[$key]);
			self::assertFalse(isset($dictionary[$key]));
			self::assertNull($dictionary[$key]);
		}

		public function testIteratability() {
			$dictionary = new GroupingDictionary();
			$dictionary['foo'] = 'bar';
			$dictionary['foo'] = 'baz';

			foreach ($dictionary as $key => $value) {
				self::assertArrayHasKey('key', $value);
				self::assertArrayHasKey('values', $value);
				self::assertEquals('foo', $value['key']);
				self::assertType('array', $value['values']);
				self::assertSame(array('bar', 'baz'), $value['values']);
			}
		}

	}
	
?>