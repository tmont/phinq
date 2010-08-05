<?php

	namespace Phinq\Tests;

	use Phinq\GroupingDictionary;
	use Phinq\Dictionary;

	class DictionaryTests extends \PHPUnit_Framework_TestCase {

		public function testArrayAccessOnGroupingDictionary() {
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
				self::assertArrayHasKey('value', $value);
				self::assertEquals('foo', $value['key']);
				self::assertType('array', $value['value']);
				self::assertSame(array('bar', 'baz'), $value['value']);
			}
		}

		public function testDefaultDictionary() {
			$dictionary = new Dictionary();
			$dictionary['foo'] = 'bar';
			self::assertEquals('bar', $dictionary['foo']);
		}

	}
	
?>