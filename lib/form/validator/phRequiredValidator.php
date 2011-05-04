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
 * A validator that makes sure something was entered
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage validator
 */
class phRequiredValidator extends phValidatorCommon
{
	const REQUIRED = 1;
	
	/**
	 * We don't want to ignore empty's like phValidatorCommon does
	 * by default
	 * @var boolean
	 */
	protected $_ignoreEmpty = false;
	/**
	 * Array's are ok
	 * @var boolean
	 */
	protected $_allowArrays = true;
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/validator/phValidatorCommon::doValidate()
	 */
	protected function doValidate($value, phValidatable $errors)
	{
		/*
		 * either the value is an array with at least one element or
		 * it has a string length > 0 for it to be valid
		 */
		$valid = ((is_array($value) && sizeof($value)>0) || (!is_array($value) && strlen($value)>0));
		if(!$valid)
		{
			$errors->addError($this->getError(self::REQUIRED));
		}
		
		return $valid;
	}
	
	protected function getValidErrorCodes()
	{
		return array(self::REQUIRED);
	}
	
	protected function getDefaultErrorMessages()
	{
		return array(self::REQUIRED=>'This is a required field, please enter a value');
	}
}