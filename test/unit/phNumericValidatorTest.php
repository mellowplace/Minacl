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
require_once dirname(__FILE__) . '/../../lib/form/phLoader.php';
phLoader::registerAutoloader();
require_once dirname(__FILE__) . '/../resources/phTestValidatable.php';
/**
 * Tests for the file validator
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage test
 */
class phNumericValidatorTest extends phTestCase
{
	/**
	 * @var phNumericValidator
	 */
	protected $_validator = null;
	/**
	 * @var phTestValidatable
	 */
	protected $_testValidatable = null;
	
	protected function setUp()
	{
		parent::setUp();
		
		$this->_testValidatable = new phTestValidatable();
		$this->_validator = new phNumericValidator();
	}
	
	public function testNotNumeric()
	{
		$this->assertFalse($this->_validator->validate('not a number', $this->_testValidatable), 'String does not validate');
		
		$errors = $this->_testValidatable->getErrors();
		$this->assertEquals(1, sizeof($errors), '1 error was added');
		$this->assertEquals(phNumericValidator::NOT_NUMERIC, $errors[0]->getCode(), 'the error was NOT_NUMERIC');
	}
	
	public function testDecimalOk()
	{
		$this->_validator->decimal(true);
		/*
		 * try a whole number, should still be valid
		 */
		$this->assertTrue($this->_validator->validate('1', $this->_testValidatable), '1 validates');
		$this->assertEquals(0, sizeof($this->_testValidatable->getErrors()), 'No errors where added for value of "1"');
		/*
		 * try a decimal
		 */
		$this->assertTrue($this->_validator->validate('1.1', $this->_testValidatable), '1.1 validates');
		$this->assertEquals(0, sizeof($this->_testValidatable->getErrors()), 'No errors where added for value of "1.1"');
	}
	
	public function testDecimalNotOk()
	{
		$this->_validator->decimal(false);
		/*
		 * try a whole number first, should be valid
		 */
		$this->assertTrue($this->_validator->validate('1', $this->_testValidatable), 'Whole number validates');
		$this->assertEquals(0, sizeof($this->_testValidatable->getErrors()), 'No errors where added for value of "1"');
		/*
		 * try decimal, should be invalid
		 */
		$this->assertFalse($this->_validator->validate('1.1', $this->_testValidatable), 'Decimal does not validate');
		$errors = $this->_testValidatable->getErrors();
		$this->assertEquals(1, sizeof($errors), '1 error added for "1.1"');
		$this->assertEquals(phNumericValidator::DECIMAL_ERROR, $errors[0]->getCode(), 'the error was DECIMAL_ERROR');
	}
	
	public function testMin()
	{
		$this->_validator->min(10);
		$this->assertTrue($this->_validator->validate('10', $this->_testValidatable), '10 validates');
		$this->assertEquals(0, sizeof($this->_testValidatable->getErrors()), 'No errors where added for value of "10"');
		/*
		 * test just below the minimum
		 */
		$this->assertFalse($this->_validator->validate('9', $this->_testValidatable), '9 does not validate');
		$errors = $this->_testValidatable->getErrors();
		$this->assertEquals(1, sizeof($errors), '1 error was added for value of 9');
		$this->assertEquals(phNumericValidator::MIN_ERROR, $errors[0]->getCode(), 'the error was MIN_ERROR');
	}
	
	public function testMax()
	{
		$this->_validator->max(10);
		$this->assertTrue($this->_validator->validate('10', $this->_testValidatable), '10 validates');
		$this->assertEquals(0, sizeof($this->_testValidatable->getErrors()), 'No errors where added for value of "10"');
		/*
		 * test just above the maximum
		 */
		$this->assertFalse($this->_validator->validate('11', $this->_testValidatable), '11 does not validate');
		$errors = $this->_testValidatable->getErrors();
		$this->assertEquals(1, sizeof($errors), '1 error was added for value of 11');
		$this->assertEquals(phNumericValidator::MAX_ERROR, $errors[0]->getCode(), 'the error was MAX_ERROR');
	}
	
	public function testMinAndMax()
	{
		$this->_validator->decimal(true)->min(5.5)->max(10.5);
		/*
		 * test cases just on the edge of the range
		 */
		$this->_validator->validate('5.5', $this->_testValidatable);
		$this->assertEquals(0, sizeof($this->_testValidatable->getErrors()), 'No errors where added for value of "5.5"');
		$this->_validator->validate('10.5', $this->_testValidatable);
		$this->assertEquals(0, sizeof($this->_testValidatable->getErrors()), 'No errors where added for value of "10.5"');
		/*
		 * test just above the max
		 */
		$this->_validator->validate('10.55', $this->_testValidatable);
		$errors = $this->_testValidatable->getErrors();
		$this->assertEquals(1, sizeof($errors), '1 error was added for value of 10.55');
		$this->assertEquals(phNumericValidator::MAX_ERROR, $errors[0]->getCode(), 'the error was MAX_ERROR');
		
		$this->_testValidatable->resetErrors();
		/*
		 * test just below the min
		 */
		$this->_validator->validate('5.49', $this->_testValidatable);
		$errors = $this->_testValidatable->getErrors();
		$this->assertEquals(1, sizeof($errors), '1 error was added for value of 5.49');
		$this->assertEquals(phNumericValidator::MIN_ERROR, $errors[0]->getCode(), 'the error was MIN_ERROR');
	}
	
	public function testEmptyOk()
	{
		$this->assertTrue($this->_validator->validate('', $this->_testValidatable), 'Empty validates');
		$this->assertEquals(0, sizeof(($this->_testValidatable)), 'Empty string validates ok');
		$this->assertTrue($this->_validator->validate(null, $this->_testValidatable), 'Null validates');
		$this->assertEquals(0, sizeof(($this->_testValidatable)), 'Null validates ok');
	}
}