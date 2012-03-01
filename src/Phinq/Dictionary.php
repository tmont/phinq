<?php

namespace Phinq;

use ArrayAccess, Iterator, Countable;

/**
 * The dictionary provides array access in a more object-capable fashion. 
 */
class Dictionary implements ArrayAccess, Iterator, Countable 
{
	/**
	 * The data store
	 * @var array
	 */
	protected $data = array();
	
	/**
	 * Position of the array pointer since traversing has to be done manually.
	 * @var int
	 */
	protected $index = 0;

	/**
	 * 
	 * @param mixed $offset
	 * @return unknown|boolean
	 */
	protected final function findOffsetIndex($offset)
	{
		foreach ($this->data as $i => $datum)
		{
			if ($datum['key'] === $offset)
			{
				return $i;
			}
		}

		return false;
	}

	/**
	 * 
	 * @param unknown_type $oldValue
	 * @param unknown_type $newValue
	 * @return unknown
	 */
	protected function createValue($oldValue, $newValue)
	{
		return $newValue;
	}

	public function offsetExists($offset)
	{
		return $this->findOffsetIndex($offset) !== false;
	}

	public function offsetGet($offset)
	{
		$index = $this->findOffsetIndex($offset);
		if ($index === false) {
			return null;
		}

		return $this->data[$index]['value'];
	}

	public function offsetSet($offset, $value)
	{
		$index = $this->findOffsetIndex($offset);
		
		if ($index === false)
		{
			$index = count($this->data);
			$this->data[$index]['key'] = $offset;
		}

		$this->data[$index]['value'] = $this->createValue(@$this->data[$index]['value'], $value);
	}

	public function offsetUnset($offset)
	{
		$index = $this->findOffsetIndex($offset);
		if ($index !== false) {
			unset($this->data[$index]);
		}
	}

	public function current() {
		return $this->data[$this->index];
	}

	public function key() {
		return $this->index;
	}

	public function next() {
		$this->index++;
	}

	public function rewind() {
		$this->index = 0;
	}

	public function valid() {
		return isset($this->data[$this->index]);
	}

	public function count() {
		return count($this->data);
	}
}