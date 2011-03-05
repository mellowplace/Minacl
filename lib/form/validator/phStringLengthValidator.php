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
 * A validator that makes sure a string is over a min length, under a max length
 * or in between the two
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage validator
 */
class phStringLengthValidator extends phValidatorCommon
{
	const INVALID = 1;
	
	private $_min = null;
	private $_max = null;
	
	public function validate($value, phValidatable $errors)
	{
		if(is_array($value))
		{
			throw new phValidatorException('I cannot validate elements that return multiple values');
		}
		
		$length = strlen($value);
		
		$valid = true;
		
		if($this->_min!==null && $length<$this->_min)
		{
			$valid = false;
		}
		else if($this->_max!==null && $length>$this->_max)
		{
			$valid = false;
		}
		
		if(!$valid)
		{
			$errors->addError($this->getError(self::INVALID));
		}
		
		return $valid;
	}
	
	/**
	 * sets the minimum character length
	 * @param integer $num
	 * @return phStringLengthValidator
	 */
	public function min($num)
	{
		$this->_min = $num;
		return $this;
	}
	
	/**
	 * sets the maximum character length
	 * @param integer $num
	 * @return phStringLengthValidator
	 */
	public function max($num)
	{
		$this->_max = $num;
		return $this;
	}
	
	protected function getValidErrorCodes()
	{
		return array(self::INVALID);
	}
	
	protected function getDefaultErrorMessages()
	{
		$message = 'Incorrect length for this value';
		return array(self::INVALID=>$message);
	}
}