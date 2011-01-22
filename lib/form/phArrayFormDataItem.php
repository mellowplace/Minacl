<?php
/*
 * phForms Project: An HTML forms library for PHP
 *          https://github.com/mellowplace/PHP-HTML-Driven-Forms/
 * Copyright (c) 2010, 2011 Rob Graham
 * 
 * This file is part of phForms.
 *
 * phForms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version.
 *
 * phForms is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public 
 * License along with phForms.  If not, see 
 * <http://www.gnu.org/licenses/>.
 */

/**
 * This data item is used to represent any array type data specified in a form
 * e.g. <input type="checkbox" name="ids[]" value="1" />
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 */
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
		
		/*
		 * build up an array of values, this will also check data being bound is valid
		 * and they have not tried to assign a value to a non-existant peice of data
		 */
		$data = $this->recursiveBind($this->_arrayTemplate, $value);
		/*
		 * this actually assigns the data to the phFormDataItem objects
		 */
		$this->recursiveAssign($this->_arrayTemplate, $data);
	}
	
	/**
	 * Clears any given value(s) in this element
	 */
	public function clear()
	{
		$this->bind(array());
	}
	
	protected function recursiveAssign($dataItems, $data)
	{
		foreach($dataItems as $k=>$d)
		{
			if(is_array($d))
			{
				$this->recursiveAssign($d, $data[$k]);
			}
			else
			{
				$d->bind($data[$k]);
			}
		}
	}
	
	protected function recursiveBind($registeredKeys, $data, $path = '')
	{
		foreach($data as $k=>$v)
		{
			$currentPath = $path . '[' . $k . ']';
			if(!array_key_exists($k, $registeredKeys))
			{
				/*
				 * We've come across some data that doesn't exist in
				 * the template but is trying to be bound to our item
				 */
				throw new phFormException("Attempting to bind invalid data, I do not have any registered keys at {$currentPath}");
			}
		}
		
		$boundData = array();
		
		foreach($registeredKeys as $k=>$v)
		{
			$currentPath = $path . '[' . $k . ']';
			
			if(array_key_exists($k, $data))
			{
				if(is_array($data[$k]))
				{
					$boundData[$k] = $this->recursiveBind($registeredKeys[$k], $data[$k], $currentPath);
				}
				else
				{
					$boundData[$k] = $data[$k];
				}
			}
			else
			{
				$boundData[$k] = null;
			}
		}
		
		return $boundData;
	}
	
	public function registerArrayKeyString($keyString, phSimpleXmlElement $element)
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
		
		$builtArray = $this->buildArray($keys, $dataItem, $element);
		
		if(!$this->isArrayKeysUnregistered($builtArray))
		{
			throw new phFormException("The array key {$keyString} has already been registered");
		}
		
		$this->_arrayTemplate = $this->arrayMergeReplaceRecursive($this->_arrayTemplate, $builtArray);
		
		return $dataItem;
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
	
	/**
	 * Recursion alert!
	 * 
	 * This recursive function takes in a key array such as
	 * 
	 * $keys[0] = 'address'
	 * $keys[1] = 'ids'
	 * $keys[2] = 1
	 * 
	 * and will return...
	 * 
	 * $builtData['address']['ids'][1] = 1;
	 * 
	 * @param array $keys single dimensional array of the keys
	 * @param phFormDataItem $dataItem a pointer to the data item the keys creates
	 * @param phSimpleXmlElement $element the element in the view that triggered the registering of an array key
	 * @param integer $level keeps track of where we are in the $keys array
	 */
	protected function buildArray($keys, &$dataItem, phSimpleXmlElement $element, $level = 0, $lastKey = null)
	{
		if(!isset($keys[$level]))
		{
			$class = $element->getDataItemClassName();
			$dataItem = new $class($lastKey); // we are at the last key so return 1, if it falls to the code below we will return an array
			return $dataItem;
		}
		
		$key = $keys[$level];
		if($key==='')
		{
			// auto key
			$key = $this->_autoKeys[$level];
			$this->_autoKeys[$level]++;
		}
		
		$builtArray = array();
		$builtArray[$key] = $this->buildArray($keys, $dataItem, $element, $level + 1, $key);
		
		return $builtArray;
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
	
	/**
	 * Rob Graham - pikied from php.net and modified slightly so it preserves numeric keys
	 * 
	 * Merges any number of arrays of any dimensions, the later overwriting
	 * previous keys, unless the key is numeric, in whitch case, duplicated
	 * values will not be added.
	 *
	 * The arrays to be merged are passed as arguments to the function.
	 *
	 * @access private
	 * @return array Resulting array, once all have been merged
	 * @author Drvali <drvali@hotmail.com>
	 * @author Rob Graham <htmlforms@mellowplace.com>
	 */
	private function arrayMergeReplaceRecursive()
	{
	    // Holds all the arrays passed
	    $params = & func_get_args ();
	   
	    // First array is used as the base, everything else overwrites on it
	    $return = array_shift ( $params );
	   
	    // Merge all arrays on the first array
	    foreach ( $params as $array ) {
	        foreach ( $array as $key => $value ) {
	            // Numeric keyed values are added (unless already there)
	            if (is_numeric ( $key ) && (! in_array ( $value, $return ))) {
	                if (is_array ( $value )) {
	                    $return [$key] = $this->arrayMergeReplaceRecursive ( $return [$key], $value );
	                } else {
	                    $return [$key] = $value;
	                }
	               
	            // String keyed values are replaced
	            } else {
	                if (isset ( $return [$key] ) && is_array ( $value ) && is_array ( $return [$key] )) {
	                    $return [$key] = $this->arrayMergeReplaceRecursive ( $return [$key], $value );
	                } else {
	                    $return [$key] = $value;
	                }
	            }
	        }
	    }
	   
	    return $return;
	}
}