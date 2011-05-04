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
 * Test class for catching errors
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage test
 */
class phTestValidatable implements phValidatable, Countable
{
	protected $_errors = array();
	
	public function validate()
	{
		return sizeof($this->_errors)==0;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phValidatable::addError()
	 */
	public function addError(phValidatorError $message)
	{
		$this->_errors[] = $message;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phValidatable::resetErrors()
	 */
	public function resetErrors()
	{
		$this->_errors = array();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phValidatable::getErrors()
	 */
	public function getErrors()
	{
		return $this->_errors;
	}
	
	public function count()
	{
		return sizeof($this->_errors);
	}
}
