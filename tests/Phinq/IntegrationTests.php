<?php

	namespace Phinq\Tests;

	use Phinq\Phinq;

	class IntegrationTests extends \PHPUnit_Framework_TestCase {

		private static function getPeople() {
			return array(
				new Person(1, 'Tommy', 'Montgomery', 'M', array(2, 3, 4, 5, 6, 7)),
				new Person(2, 'Bob', 'Montgomery', 'M', array(1, 2, 5, 7)),
				new Person(3, 'Joe', 'Montgomery', 'M', array(1, 2, 7)),
				new Person(4, 'Joe', 'Blow', 'M', array()),
				new Person(5, 'MC', 'Hammer', 'M', array(1, 2, 3)),
				new Person(6, 'Veronica', 'Lodge', 'F', array(1, 7)),
				new Person(7, 'Veronica', 'Mars', 'F', array(1, 5, 6)),
			);
		}

		public function testWhereWithSelectAndAggregate() {
			$tommysFemaleFriends = Phinq::create(self::getPeople())
				->where(function($person) { return Phinq::create($person->friends)->contains(1); })
				->where(function($person) { return $person->sex === 'F'; })
				->select(function($person) { return $person->firstName . ' ' . $person->lastName; })
				->aggregate(function($current, $next) { return $current . $next . "\n"; });

			$expected = <<<PEOPLE
Veronica Lodge
Veronica Mars

PEOPLE;

			self::assertEquals($expected, $tommysFemaleFriends);
		}

		public function testSelectMany() {
			$uniqueFriends = Phinq::create(self::getPeople())
				->selectMany(function($person) { return $person->friends; })
				->distinct();

			self::assertSame(array(2, 3, 4, 5, 6, 7, 1), $uniqueFriends->toArray());
		}

	}

	class Person {
		public $id;
		public $firstName;
		public $lastName;
		public $sex;
		public $friends;

		public function __construct($id, $firstName, $lastName, $sex, array $friends = array()) {
			$this->id = $id;
			$this->firstName = $firstName;
			$this->lastName = $lastName;
			$this->sex = $sex;
			$this->friends = $friends;
		}
	}

	
?>