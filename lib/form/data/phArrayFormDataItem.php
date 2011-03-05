<?php
/*
 * Minacl Project: An HTML forms library for PHP
 *          https://github.com/mellowplace/PHP-HTML-Driven-Forms/
 * Copyright (c) 2010, 2011 Rob Graham
 * 
 * This file is part of Minacl.
 *
 * Minacl is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version.
 *
 * Minacl is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public 
 * License along with Minacl.  If not, see 
 * <http://www.gnu.org/licenses/>.
 */

/**
 * This data item is used to represent any array type data specified in a form
 * e.g. <input type="input" name="address[city]" value="" />
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage data
 */
class phArrayFormDataItem extends phFormDataItem implements ArrayAccess, Countable
{
	protected $_autoKey = null;
	
	protected $_arrayTemplate = array();
	
	public function bind($values)
	{
		if($values===null)
		{
			$values = array();
		}
		
		if(!is_array($values))
		{
			throw new phFormException("Trying to bind a value that is not an array to {$this->_name}");
		}
		
		/*
		 * check all array keys registered
		 */
		foreach($values as $k=>$v)
		{
			if(!array_key_exists($k, $this->_arrayTemplate))
			{
				throw new phFormException("Attempting to bind invalid data, I do not have any registered keys at {$k}");
			}
		}
		
		/*
		 * now bind including values that have not been posted
		 * (checkboxes don't send values if they are not checked
		 * but we still need to bind nothing to the dataitems so
		 * the elements can uncheck etc)
		 */
		foreach($this->_arrayTemplate as $k=>$v)
		{
			if(array_key_exists($k, $values))
			{
				$value = $values[$k];
			}
			else
			{
				$value = null;
			}
			
			$v->bind($value);
		}
	}
	
	/**
	 * Clears any given value(s) in this element
	 */
	public function clear()
	{
		$this->bind(array());
	}
	
	public function getNextAutoKey()
	{
		return $this->_autoKey===null ? 0 : $this->_autoKey;
	}
	
	public function registerArrayKey(phArrayKeyInfo $key, phData $dataItem)
	{
		/*
		 * First check that the user has not tried to mix auto keys (e.g. data[]) with
		 * specified ones (e.g. data[6] or data[name])
		 */
		if(
			($this->_autoKey!==null && !$key->isAutoKey()) || // already auto keys and trying to register normal key 
			(sizeof($this->_arrayTemplate)>0 && $this->_autoKey===null && $key->isAutoKey()) // already normal keys registered and trying to register auto key
		)
		{
			throw new phFormException("You cannot mix auto keys ([]) with specified keys: at {$this->_name}, level {$k}");
		}
		else if($key->isAutoKey() && $this->_autoKey===null)
		{
			$this->_autoKey = 0;
		}
		
		$arrayKey = $key->getKey();
		if($key->isAutoKey())
		{
			$arrayKey = $this->_autoKey;
			$this->_autoKey++;
		}
		
		if(array_key_exists($arrayKey, $this->_arrayTemplate))
		{
			throw new phFormException("the array with key {$key->getKey()} has already been registered");
		}
		
		$this->_arrayTemplate[$arrayKey] = $dataItem;
		
		return $dataItem;
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
		return sizeof($this->_arrayTemplate);
	}
}