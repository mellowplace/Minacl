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
	
	protected abstract function getValidErrorCodes();
	
	protected abstract function getDefaultErrorMessages();
}