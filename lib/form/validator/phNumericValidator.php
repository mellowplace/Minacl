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
 * A validator that validates numbers, either whole or decimal and can assert
 * that a number is above a minimum and/or below a maximum
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage validator
 */
class phNumericValidator extends phValidatorCommon
{
	/**
	 * If the value is below the minimum this error will occur
	 * @var int
	 */
	const MIN_ERROR = 1;
	/**
	 * If the value is above the maximum this error will occur
	 * @var int
	 */
	const MAX_ERROR = 2;
	/**
	 * If the number is a decimal and that is not allowed, this error will occur
	 * @var int
	 */
	const DECIMAL_ERROR = 3;
	/**
	 * If the number is not numeric this error will occur
	 * @var int
	 */
	const NOT_NUMERIC = 4;
	
	/**
	 * Are decimals allowed?
	 * @var boolean
	 */
	protected $_decimal = true;
	
	/**
	 * Should the value be greater or equal to a minimum value?
	 * @var float
	 */
	protected $_min = null;
	
	/**
	 * Should the value be less or equal to a maximum value?
	 * @var float
	 */
	protected $_max = null;
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/validator/phValidatorCommon::doValidate()
	 */
	protected function doValidate($value, phValidatable $errors)
	{
		if(!is_numeric($value))
		{
			$errors->addError($this->getError(self::NOT_NUMERIC));
			return false;
		}
		
		if(strpos($value, '.')!==false && !$this->_decimal)
		{
			// decimals not allowed and this value is a decimal
			$errors->addError($this->getError(self::DECIMAL_ERROR));
			return false;
		}
		
		if($this->_min!==null && $value<$this->_min)
		{
			// value is below required minimum
			$errors->addError($this->getError(self::MIN_ERROR, array('%min%'=>$this->_min)));
			return false;
		}
		
		if($this->_max!==null && $value>$this->_max)
		{
			// value is above required minimum
			$errors->addError($this->getError(self::MAX_ERROR, array('%max%'=>$this->_max)));
			return false;
		}
		
		return true;
	}
	
	/**
	 * Should the validator pass decimals?
	 * @param boolean $decimal true if decimal values are ok
	 * @return phNumericValidator
	 */
	public function decimal($decimal)
	{
		$this->_decimal = $decimal;
		return $this;
	}
	
	/**
	 * Is there a minimum value?
	 * @param integer $min if set then the value must be >= to $min to pass
	 * @return phNumericValidator
	 */
	public function min($min)
	{
		$this->_min = $min;
		return $this;
	}
	
	/**
	 * Is there a maximum value?
	 * @param integer $max if set then the value must be <= to $max to pass
	 * @return phNumericValidator
	 */
	public function max($max)
	{
		$this->_max = $max;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/validator/phValidatorCommon::getValidErrorCodes()
	 */
	protected function getValidErrorCodes()
	{
		return array(
			self::MIN_ERROR,
			self::MAX_ERROR,
			self::DECIMAL_ERROR,
			self::NOT_NUMERIC
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/validator/phValidatorCommon::getDefaultErrorMessages()
	 */
	protected function getDefaultErrorMessages()
	{
		return array(
			self::MIN_ERROR => 'The number must be equal to or greater than %min%',
			self::MAX_ERROR => 'The number must be equal to or less than %max%',
			self::DECIMAL_ERROR => 'Only whole numbers are allowed',
			self::NOT_NUMERIC => 'The value must be numeric'
		);
	}
}