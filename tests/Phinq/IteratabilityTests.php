<?php

	namespace Phinq\Tests;

	use Phinq\Phinq;

	class IteratabilityTests extends \PHPUnit_Framework_TestCase {

		public function testForeachability() {
			$collection = Phinq::create(range(1, 6));

			self::assertAttributeEquals(false, 'isDirty', $collection);
			$collection->concat(array());
			self::assertAttributeEquals(true, 'isDirty', $collection);

			foreach ($collection as $key => $value) {
				self::assertEquals($key + 1, $value);
			}

			self::assertAttributeEquals(false, 'isDirty', $collection);

			foreach ($collection as $key => $value) {
				self::assertEquals($key + 1, $value);
			}
		}
	
	}
	
?>