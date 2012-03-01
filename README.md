# phinq - LINQ for PHP

This project was originally created Tommy Montgomery in 2010 and was a faithful port of .NET's LINQ library (circa 4.0). It was ported over to Github from Google Code and is now under active development to bring additional capabilites into the project.

## Current Development ##

Currently the code provides a strong LINQ to Objects impelementation but isn't structured to handle other adapters such as an XML or database provider.

- Refactor existing code to be **Phinq to Objects** and allow for additional query adapters.
- Implement **Phinq to XML**.
- Implement **Phinq to SQL**.


## Requirements

* PHP 5.3 or higher
* A PSR-0 compliant autoloader.

## Please Read

The original version of Phinq shipped with its own autoloader for classes and tests. This has been removed in favor of being more "framework friendly" and so can slot easily into any framework that implements a PSR-0 compliant autoloader.

If you are not using a framework you will still need to use an autoloader since Phinq class files do not explicitly load one another.


## Examples

### Basic Usage
```php

//Import the Phinq namespace and main class
use Phinq\Phinq;

//suppose you have some sort of collection
$payments = array(10.5, 11.94, 9.3, 0, 17.1, 10.5, 0);

//let's do something to it
$paymentQuery = Phinq::create($payments)
  ->where(function($payment) { return $payment !== 0; }) //non-zero
  ->orderBy(function($payment) { return $payment; }); //sorted ascending

//nothing happens until we evaluate the expressions by calling toArray()
$nonZeroOrderedPayments = $paymentQuery->toArray();
print_r($nonZeroOrderedPayments);
/*
Array
(
    [0] => 9.3
    [1] => 10.5
    [2] => 10.5
    [3] => 11.94
    [4] => 17.1
)
*/

//non-zero, ordered and distinct
print_r($paymentQuery->distinct()->toArray());
/*
Array
(
    [0] => 9.3
    [1] => 10.5
    [2] => 11.94
    [3] => 17.1
)
*/
```

### Grouping using objects
```php
//say we had a Person struct, and each person has a place of residence
class Person {
  public $id;
  public $name;
  public $residence;
  
  public function __construct($id, $name, State $state) {
    $this->id = $id;
    $this->name = $name;
    $this->residence = $state;
  }
}

class State {
  public $id;
  public $name;
  public $region;
  public $code;
  
  public function __construct($id, $name, $region, $code) {
    $this->id = $id;
    $this->name = $name;
    $this->region = $region;
    $this->code = $code;
  }
}
  
//...and we have these people who live in these states
$people = array(
  new Person(1, 'Tommy', new State(1, 'California', 'SW', 'CA')),
  new Person(2, 'Bobby', new State(2, 'Washington', 'NW', 'WA')),
  new Person(3, 'Joey', new State(2, 'Washington', 'NW', 'WA')),
  new Person(4, 'Jerry', new State(3, 'New York', 'NE', 'NY')),
  new Person(3, 'Dubya', new State(4, 'Texas', 'S', 'TX'))
);
  
//...and we want to group them by region
echo Phinq::create($people)
  ->groupBy(function($person) { return $person->residence->region; })
  ->select(function($grouping) { 
    //$grouping is an instance of Phinq\Grouping, which inherits from Phinq
    //it has a getKey() method which returns the key used to perform the grouping
    $obj = new stdClass();
    $obj->people = $grouping;
    $obj->region = $grouping->getKey();
    return $obj;
  })->orderBy(function($obj) { return $obj->people->count(); }, true /* descending */)
  ->aggregate(function($current, $next) { 
    $count = $next->people->count();
    return $current . sprintf(
      "%d %s (%s) live in the %s region\n",
      $count,
      $count === 1 ? 'person' : 'people',
      $next->people->aggregate(function($current, $next) {
        if ($current !== null) {
          $current .= ', ';
        }
        return $current . sprintf('%s [%s]', $next->name, $next->residence->code);
      }),
      $next->region
    );
  });

/*
2 people (Bobby [WA], Joey [WA]) live in the NW region
1 person (Dubya [TX]) live in the S region
1 person (Tommy [CA]) live in the SW region
1 person (Jerry [NY]) live in the NE region
*/
```

The C# equivalent would be approximately:

```c#
people
  .GroupBy(person => person.Residence.Region)
  .Select(grouping => new { Region = grouping.Key, People = grouping })
  .OrderBy(obj => obj.People.Count())
  .Aggregate((current, next) => {
    var count = next.People.Count();
    return current + string.Format(
      "{0} {1} ({2}) live in the {3} region",
      count,
      count == 1 ? "person" : "people",
      next.People.Aggregate((personString, nextPerson) {
        if (!string.IsNullOrEmpty(personString)) {
          personString += ", ";
        }

        return personString + string.Format("{0} [{1}]", nextPerson.Name, nextPerson.Residence.Code);
      }),
      next.Region
    );
  });
```

## LINQ methods implemented:

* `Aggregate()`
* `All()`
* `Any()`
* `Average()`
* `Cast()`
* `Concat()`
* `Contains()`
* `Count()`
* `DefaultIfEmpty()`
* `Distinct()`
* `ElementAt()`
* `ElementAtOrDefault()`
* `Except()`
* `First()`
* `FirstOrDefault()`
* `GroupBy()`
* `GroupJoin()`
* `Intersect()`
* `Join()`
* `Last()`
* `LastOrDefault()`
* `Max()`
* `Min()`
* `OfType()`
* `OrderBy()`
* `Reverse()`
* `Select()`
* `SelectMany()`
* `SequenceEqual()`
* `Single()`
* `SingleOrDefault()`
* `Skip()`
* `SkipWhile()`
* `Sum()`
* `Take()`
* `TakeWhile()`
* `ThenBy()`
* `ToArray()`
* `ToDictionary()`
* `Union()`
* `Where()`
* `Zip()` 

Extra methods:

* `walk()` 

LINQ methods to be implemented: None.

LINQ methods not implemented:

* `AsEnumerable()`
* `AsParallel()`
* `AsQueryable()`
* `LongCount()`
* `OrderByDescending()` - implemented as an optional argument to orderBy()
* `ThenByDescending()` - implemented as an optional argument to thenBy()
* `ToList()`
* `ToLookup()` 

