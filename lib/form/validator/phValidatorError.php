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
 * Represents and error triggered by a validator
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage validator
 */
class phValidatorError
{
	protected $_message = null;
	protected $_code = null;
	/**
	 * A reference to the validator that triggered the error
	 * @var phValidator
	 */
	protected $_validator = null;
	
	public function __construct($message, $code, phValidator $validator)
	{
		$this->_message = $message;
		$this->_code = $code;
		$this->_validator = $validator;
	}
	
	/**
	 * @return phValidator the validator that triggered the error
	 */
	public function getValidator()
	{
		return $this->_validator;
	}
	
	/**
	 * @return string the error message
	 */
	public function getMessage()
	{
		return $this->_message;
	}
	
	public function setMessage($message)
	{
		$this->_message = $message;
	}
	
	/**
	 * @return int the error code
	 */
	public function getCode()
	{
		return $this->_code;
	}
}