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
 * Common base for a basic validator that provides error messages
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage validator
 */
abstract class phValidatorCommon implements phValidator
{
	protected $_errors = array();
	
	/**
	 * If true then validation will not occur for empty values
	 * @var boolean
	 */
	protected $_ignoreEmpty = true;
	
	/**
	 * If true then the validator can validate array values, if false
	 * an exception is thrown if the value is an array
	 * 
	 * @var boolean
	 */
	protected $_allowArrays = false;
	
	public function __construct(array $errors = array())
	{
		foreach($errors as $code=>$e)
		{
			$this->setError($code, $e);
		}
	}
	
	public function setError($code, $message)
	{
		if(!in_array($code, $this->getValidErrorCodes()))
		{
			throw new phValidatorException("The error code '{$code}' is not valid for this validator");
		}
		
		$this->_errors[$code] = new phValidatorError($message, $code, $this);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/validator/phValidator::validate()
	 */
	public function validate($value, phValidatable $errors)
	{
		/*
		 * if we don't allow arrays and $value is an array throw a wobbler
		 */
		if(!$this->_allowArrays && is_array($value))
		{
			throw new phValidatorException('I cannot validate elements that return multiple values');
		}
		/*
		 * if we ignore empties and the value is empty then we pass the value
		 */
		if($this->_ignoreEmpty && $this->isEmpty($value))
		{
			return true;
		}
		
		return $this->doValidate($value, $errors);
	}
	
	/**
	 * @param integer $code
	 * @return phValidatorError
	 */
	protected function getError($code, $replacements = array())
	{
		if(isset($this->_errors[$code]))
		{
			$error = $this->_errors[$code];
		}
		else
		{
			$defaults = $this->getDefaultErrorMessages();
			$error = new phValidatorError($defaults[$code], $code, $this);
		}
		
		$message = $error->getMessage();
		
		foreach($replacements as $search=>$replace)
		{
			$message = str_replace($search, $replace, $message);
		}
		
		$error->setMessage($message);
		
		return $error;
	}
	
	/**
	 * Decides if a value is empty ('' or null is).  Used to decide whether validation is
	 * needed
	 * 
	 * @param mixed $value
	 * @return boolean
	 */
	protected function isEmpty($value)
	{
		/*
		 * empty arrays, strings or null values are all empty
		 */
		if(	(!is_array($value) && ($value==='' || $value===null)) ||
			(is_array($value) && sizeof($value)===0) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Implement and perform the actual validation in this method (it's called
	 * by validate)
	 * 
	 * @param string $value
	 * @param array $errors
	 */
	protected abstract function doValidate($value, phValidatable $errors);
	
	protected abstract function getValidErrorCodes();
	
	protected abstract function getDefaultErrorMessages();
}