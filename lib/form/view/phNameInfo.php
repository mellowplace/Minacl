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
 * This class can parse name strings
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage view
 */
class phNameInfo
{
	protected $_name = null;
	protected $_array = false;
	protected $_arrayKeyString = null;
	protected $_valid = false;
	protected $_arrayInfo = null;
	protected $_nameString = '';
	
	public function __construct($nameString)
	{
		$this->_nameString = $nameString;
		
		$numMatched = preg_match('/^([a-zA-Z\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*?)((\[[a-zA-Z0-9_\x7f-\xff]*?\])*)?$/', $nameString, $matches);
		
		if($numMatched!==0)
		{
			$name = $matches[1];
			$array = false;
			$arrayParts = '';
			
			if(isset($matches[2]) && strlen($matches[2]) > 0)
			{
				$array = true;
				$arrayParts = $matches[2];
				$this->_arrayInfo = new phArrayInfo($arrayParts);
			}
			
			$this->_name = $name;
			$this->_arrayKeyString = $arrayParts;
			$this->_array = $array;
			$this->_valid = true;
		}
	}
	
	public function getName()
	{
		return $this->_name;
	}
	
	public function isArray()
	{
		return $this->_array;
	}
	
	public function getArrayKeyString()
	{
		return $this->_arrayKeyString;
	}
	
	/**
	 * @return phArrayInfo
	 */
	public function getArrayInfo()
	{
		return $this->_arrayInfo;
	}
	
	public function isValid()
	{
		return $this->_valid;
	}
	
	public function getFullName()
	{
		return $this->_nameString;
	}
	
	public function __toString()
	{
		return $this->_nameString;
	}
}