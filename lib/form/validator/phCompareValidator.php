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
 * A validator that compares a value with a data item using an operator
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage validator
 */
class phCompareValidator extends phValidatorCommon
{
	/*
	 * comparison constants
	 */
	const EQUAL = 1;
	const NOT_EQUAL = 2;
	const GREATER_THAN = 3;
	const LESS_THAN = 4;
	const GREATER_EQUAL = 5;
	const LESS_EQUAL = 6;
	/*
	 * error constants
	 */
	const INVALID = 1;
	
	/**
	 * @var phFormDataItem
	 */
	protected $_compareWith = null;
	protected $_operator = 1;
	
	/**
	 * Compares a value with a data field using an operator.
	 * 
	 * @param $compareWith phData|mixed can compare to another field or a scalar value
	 * @param $operator int the operator to compare with, e.g. =,!=,<,>
	 * @param $errors array
	 */
	public function __construct($compareWith, $operator, $errors = array())
	{
		if(!($compareWith instanceof phData) && !is_scalar($compareWith))
		{
			throw new phValidatorException('I can only compare phData instances or scalar values');
		}
		
		$this->_compareWith = $compareWith;
		$this->_operator = $operator;
		
		parent::__construct($errors);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/validator/phValidator::validate()
	 */
	public function validate($value, phValidatable $errors)
	{
		$compareValue = $this->_compareWith instanceof phData ? $this->_compareWith->getValue() : $this->_compareWith;
		$valid = false;
		
		switch($this->_operator)
		{
			case self::EQUAL:
				$valid = $value==$compareValue;
				break;
			case self::NOT_EQUAL:
				$valid = $value!=$compareValue;
				break;
			case self::GREATER_THAN:
				$valid = $value>$compareValue;
				break;
			case self::GREATER_EQUAL:
				$valid = $value>=$compareValue;
				break;
			case self::LESS_THAN:
				$valid = $value<$compareValue;
				break;
			case self::LESS_EQUAL:
				$valid = $value<=$compareValue;
				break;
		}
		
		if(!$valid)
		{
			$errors->addError($this->getError(self::INVALID));
		}
		
		return $valid;
	}
	
	protected function getValidErrorCodes()
	{
		return array(self::INVALID);
	}
	
	protected function getDefaultErrorMessages()
	{
		return array(self::INVALID=>'The values did not pass the comparison');
	}
}