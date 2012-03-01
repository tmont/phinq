<?php

namespace Phinq;

use \InvalidArgumentException;

/**
 * 
 */
class CastQuery implements Query 
{
	private $caster;

	/**
	 * Construct a new instance of this object.
	 * @param unknown $type
	 * @throws InvalidArgumentException
	 */
	public function __construct($type)
	{
		switch (strtolower($type))
		{
			case 'string':
		        $this->caster = function($value) { return (string)$value; };
		        break;
			case 'int':
			case 'integer':
		        $this->caster = function($value) { return (int)$value; };
		        break;
			case 'float':
			case 'double':
			case 'real':
		        $this->caster = function($value) { return (float)$value; };
		        break;
			case 'array':
		        $this->caster = function($value) { return (array)$value; };
		        break;
			case 'bool':
			case 'boolean':
		        $this->caster = function($value) { return (bool)$value; };
		        break;
			case 'object':
		        $this->caster = function($value) { return (object)$value; };
		        break;
			case 'null':
			case 'unset':
				$this->caster = function($value) { return (unset)$value; };
		        break;
			case 'binary':
		        $this->caster = function($value) { return (binary)$value; };
		        break;
			default:
		        throw new InvalidArgumentException('1st argument must be one of string, int, float, bool, array, object, binary or null');
		}
	}

	/**
	 * 
	 * @see Phinq.Query::execute()
	 */
	public function execute(array $collection)
	{
		$caster = $this->caster;

		array_walk($collection, function(&$value) use ($caster) {
			$value = $caster($value);
		});

		return $collection;
	}
}