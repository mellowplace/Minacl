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
 * Array info takes a key string like [name][first][] and provides
 * information about it
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage view
 */
class phArrayInfo
{
	protected $_keys = array();
	protected $_valid = false;
	
	public function __construct($keyString)
	{
		preg_match_all('/\[([a-zA-Z0-9_\x7f-\xff]*?)\]/', $keyString, $matches);
		
		$keys = array();
		
		if(array_key_exists(1, $matches))
		{
			$this->_valid = true;
			
			foreach($matches[1] as $key)
			{
				if(!strlen($key))
				{
					$keys[] = new phArrayKeyInfo($key, true, phArrayKeyInfo::NUMERIC);
				}
				else if(is_numeric($key))
				{
					$keys[] = new phArrayKeyInfo((int)$key, false, phArrayKeyInfo::NUMERIC);
				}
				else
				{
					$keys[] = new phArrayKeyInfo($key, false, phArrayKeyInfo::STRING);
				}
			}
		}
		
		$this->_keys = $keys;
	}
	
	public function getKeys()
	{
		return $this->_keys;
	}
	
	public function isValid()
	{
		return $this->_valid;
	}
	
	public function getKeyInfo($index)
	{
		if(!array_key_exists($index, $this->_keys))
		{
			throw new phFormException("There is no key registered at index {$index}");
		}
		
		return $this->_keys[$index];
	}
}


/**
 * Represents information about a single key
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage view
 */
class phArrayKeyInfo
{
	const NUMERIC = 1;
	const STRING = 2;
	
	private $_key = null;
	private $_autoKey = false;
	private $_type = null;
	
	public function __construct($key, $autoKey = false, $type = self::NUMERIC)
	{
		$this->_key = $key;
		$this->_autoKey = $autoKey;
		$this->_type = $type;
	}
	
	public function getKey()
	{
		return $this->_key;
	}
	
	public function getType()
	{
		return $this->_type;
	}
	
	public function isAutoKey()
	{
		return $this->_autoKey;
	}
}