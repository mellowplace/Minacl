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
require_once 'phTestCase.php';
require_once realpath(dirname(__FILE__)) . '/../../lib/form/phLoader.php';
phLoader::registerAutoloader();
require_once realpath(dirname(__FILE__)) . '/../resources/phTestValidatable.php';

/**
 * tests for the phValidatorCommon class
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage test
 */
class phValidatorCommonTest extends phTestCase
{
	/**
	 * @var phTestValidatorCommon
	 */
	protected $validator = null;
	/**
	 * @var phTestValidatable
	 */
	protected $errors = null;
	
	public function setUp()
	{
		$this->validator = new phTestValidatorCommon();
		$this->errors = new phTestValidatable();
	}
	
	/**
	 * Check that validating an array when they are not allowed throws an exception
	 * 
	 * @expectedException phValidatorException
	 */
	public function testArrayNotAllowed()
	{
		$this->validator->validate(array(1), $this->errors);
	}
	
	/**
	 * Same as above but with empty array when empty is allowed
	 * 
	 * @expectedException phValidatorException
	 */
	public function testArrayNotAllowed2()
	{
		$this->validator->validate(array(), $this->errors);
	}
	
	public function testIgnoreEmpty()
	{
		/*
		 * set the validator to fail and check that it still passes
		 * with empty values
		 */
		$this->validator->validateOk = false;
		$this->validator->setArraysAllowed(true);
		$this->assertTrue($this->validator->validate(null, $this->errors), 'validates null ok');
		$this->assertTrue($this->validator->validate('', $this->errors), 'validates empty string ok');
		$this->assertTrue($this->validator->validate(array(), $this->errors), 'validates empty array ok');
	}
	
	/**
	 * test empty values are validated when the validator is set not to ignore them
	 */
	public function testValidateEmpty()
	{
		$this->validator->validateOk = false;
		$this->validator->setArraysAllowed(true);
		$this->validator->setIgnoreEmpty(false);
		
		$this->assertFalse($this->validator->validate(null, $this->errors), 'fails null ok');
		$this->assertFalse($this->validator->validate('', $this->errors), 'fails empty string ok');
		$this->assertFalse($this->validator->validate(array(), $this->errors), 'fails empty array ok');
	}
}

class phTestValidatorCommon extends phValidatorCommon
{
	public $validateOk = true;
	
	protected function doValidate($value, phValidatable $errors)
	{
		return $this->validateOk;
	}
	
	public function setArraysAllowed($allowed)
	{
		$this->_allowArrays = $allowed;
	}
	
	public function setIgnoreEmpty($ignore)
	{
		$this->_ignoreEmpty = $ignore;
	}
	
	protected function getValidErrorCodes()
	{
		return array(1);
	}
	
	protected function getDefaultErrorMessages()
	{
		return array(1=>'test');
	}
}