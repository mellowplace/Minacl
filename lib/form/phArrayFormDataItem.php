<?php
class phArrayFormDataItem extends phFormDataItem implements ArrayAccess, Countable
{	
	protected $_autoKeys = array();
	
	protected $_arrayTemplate = array();
	
	public function bind($value)
	{
		if(!is_array($value))
		{
			throw new phFormException("Trying to bind a value that is not an array to {$this->_name}");
		}
		
		return parent::bind($value);
	}
	
	public function registerArrayKeyString($keyString)
	{
		$keys = $this->extractArrayKeys($keyString);
		
		/*
		 * First check that the user has not tried to mix auto keys (e.g. data[]) with
		 * specified ones (e.g. data[6] or data[name])
		 */
		foreach($keys as $k=>$v)
		{
			if(isset($this->_autoKeys[$k]) && is_numeric($this->_autoKeys[$k]) && $v!=='')
			{
				throw new phFormException("You cannot mix auto keys ([]) with specified keys: at {$this->_name}, level {$k}");
			}
			else if($v==='' && !array_key_exists($k, $this->_autoKeys))
			{
				$this->_autoKeys[$k] = 0;
			}
		}
		
		$builtArray = $this->buildArray($keys);
		
		if(!$this->isArrayKeysUnregistered($builtArray))
		{
			throw new phFormException("The array key {$keyString} has already been registered");
		}
		
		$this->_arrayTemplate = array_merge($this->_arrayTemplate, $builtArray);
	}
	
	protected function isArrayKeysUnregistered($keys, $currentKeys = null, $currentRegistered = null)
	{
		if($currentKeys===null)
		{
			$currentKeys = $keys;
		}
		
		if($currentRegistered===null)
		{
			$currentRegistered = $this->_arrayTemplate;
		}
		
		foreach($currentKeys as $k=>$v)
		{
			if(!array_key_exists($k, $currentRegistered))
			{
				return true;
			}
			
			if(!is_array($v))
			{
				// we are at last element and it exists in the registered array
				return false;
			}
		}
		
		return $this->isArrayKeysUnregistered($keys, $currentKeys[$k], $currentRegistered[$k]);
	}
	
	protected function buildArray($keys, $level = 0, $builtArray = array())
	{
		if(!isset($keys[$level]))
		{
			return $builtArray;
		}
		
		$key = $keys[$level];
		if($key==='')
		{
			// auto key
			$key = $this->_autoKeys[$level];
			$this->_autoKeys[$level]++;
		}
		
		$builtArray[$key] = 1;
		
		return $this->buildArray($keys, $level + 1, $builtArray);
	}
	
	protected function extractArrayKeys($keyString)
	{
		$numMatched = preg_match_all('/(\[([a-zA-Z0-9_\x7f-\xff]*?)\])/', $keyString, $matches);
		if(!isset($matches[2]))
		{
			throw new phFormException("Invalid array key string '{$keyString}'");
		}
		
		return $matches[2];
	}
	
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->_arrayTemplate);
	}
	
	public function offsetGet($offset)
	{
		return $this->_arrayTemplate[$offset];
	}
	
	public function offsetSet ($offset, $value)
	{
		throw new phFormException('You cannot set array values on this class, all setting of data should bew done through bind');
	}
	
	public function offsetUnset ($offset)
	{
		throw new phFormException('You cannot unset array values on this class, all setting of data should bew done through bind');
	}
	
	public function count()
	{
		if(!is_array($this->_arrayTemplate))
		{
			return 0;
		}
		
		return sizeof($this->_arrayTemplate);
	}
}