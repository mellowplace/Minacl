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

class phValidatorTest extends phTestCase
{
	public function setUp()
	{
		parent::setUp();
		
		$this->form = new TestForm('test', 'simpleTestView');
	}
	
	public function testFormFail()
	{
		$this->form->username->setValidator(new TestValidatorFail('required'));
		$this->form->bindAndValidate(array(
			'username'=>'fail',
			'password'=>'fail'
		));
		$this->assertFalse($this->form->isValid(), 'Form is correctly not valid');
	}
	
	/**
	 * Like the test above but makes sure that the forms validator fails
	 */
	public function testFormValidatorFail()
	{
		$this->form->setValidator(new TestValidatorFail('The form failed'));
		$this->form->bindAndValidate(array(
			'username'=>'fail',
			'password'=>'fail'
		));
		$this->assertFalse($this->form->isValid(), 'Form is correctly not valid');
		$this->assertEquals(array('The form failed'), $this->extractErrorMessages($this->form->getErrors()), 'Error message correctly set');
	}
	
	public function testEmptyPostFormFail()
	{
		$this->form->username->setValidator(new TestValidatorFail('required'));
		$this->form->username->bind('');
		$this->form->bindAndValidate(array());
		$this->assertFalse($this->form->isValid(), 'Form is correctly not valid');
	}
	
	public function testElementErrorMessages()
	{
		$this->form->username->setValidator(new TestValidatorFail('This field is required'));
		$this->form->bindAndValidate(array(
			'username'=>'fail',
			'password'=>'fail'
		));
		
		$errors = $this->form->username->getErrors();
		$messages = $this->extractErrorMessages($errors);
		
		$this->assertTrue(in_array('This field is required', $messages), 
			'The error message is returned in form->username->getErrors');
	}
	
	public function testFormErrorMessages()
	{
		$this->form->username->setValidator(new TestValidatorFail('This field is required'));
		$this->form->bindAndValidate(array(
			'username'=>'fail',
			'password'=>'fail'
		));
		
		$errors = $this->form->getErrors();
		$messages = $this->extractErrorMessages($errors);
		$this->assertTrue(in_array('This field is required', $messages), 
			'The error message is returned in form->getErrors');
	}
	
	public function testViewErrorMessages()
	{
		$this->form->username->setValidator(new TestValidatorFail('This field is required'));
		$this->form->bindAndValidate(array(
			'username'=>'fail',
			'password'=>'fail'
		));
		
		$view = $this->form->getView();
		$this->assertTrue(in_array('This field is required', $view->error('username')), 
			'The error message is returned in view->error(\'username\')');
	}
	
	public function testFormPass()
	{
		$this->form->username->setValidator(new TestValidatorPass());
		$this->form->bindAndValidate(array(
			'username'=>'pass',
			'password'=>'pass'
		));
		$this->assertTrue($this->form->isValid(), 'Form is correctly valid');
	}
	
	/**
	 * Like the above test but also checks the validator passes
	 */
	public function testFormValidatorPass()
	{
		$this->form->setValidator(new TestValidatorPass());
		$this->form->bindAndValidate(array(
			'username'=>'pass',
			'password'=>'pass'
		));
		$this->assertTrue($this->form->isValid(), 'Form is correctly valid');
	}
	
	public function testRequiredValidatorFail()
	{
		$this->form->username->setValidator(new phRequiredValidator(
			array(phRequiredValidator::REQUIRED=>'Username is required')
		));
		$this->form->bindAndValidate(array(
			'username'=>'',
			'password'=>'fail'
		));
		
		$this->assertFalse($this->form->username->validate(), 'The username is correctly invalid');
		$messages = $this->extractErrorMessages($this->form->username->getErrors());
		$this->assertTrue(in_array('Username is required', $messages),
			'The username required validator error was set properly');
	}
	
	public function testRequiredValidatorPass()
	{
		$this->form->username->setValidator(new phRequiredValidator(array(
			phRequiredValidator::REQUIRED=>"Username is required"
		)));
		$this->form->bindAndValidate(array(
			'username'=>'here',
			'password'=>'fail'
		));
		
		$this->assertTrue($this->form->username->validate(), 'The username is valid');
	}
	
	public function testRequiredValidatorArray()
	{
		$val = new phRequiredValidator();
		$errors = new phTestValidatable();
		
		$this->assertTrue($val->validate(array(0), $errors), 'required validator passes an array with an element');
		$this->assertFalse($val->validate(array(), $errors), 'required validator fails an empty array');
	}
	
	public function testStringLengthValidator()
	{
		$strVal = new phStringLengthValidator(array(
			phStringLengthValidator::INVALID=>"Please enter a string between 6 and 8 characters long"
		));
		$strVal->min(6)->max(8);
		
		$username = new phFormDataItem('username');
		$username->setValidator($strVal);
		
		$username->bind('short');
		$this->assertFalse($strVal->validate($username->getValue(), $username), 'The validator is correctly not valid');
		$messages = $this->extractErrorMessages($username->getErrors());
		$this->assertTrue(in_array('Please enter a string between 6 and 8 characters long', 
							$messages), 'error message set properly');
		
		$username->bind('tooloooooooong');
		$this->assertFalse($strVal->validate($username->getValue(), $username), 'The validator is correctly not valid');
		
		$username->bind('justfine');
		$this->assertTrue($strVal->validate($username->getValue(), $username), 'The validator is valid');
		
		$strVal->min(6)->max(null);
		$username->bind('short');
		$this->assertFalse($strVal->validate($username->getValue(), $username), 'The validator is correctly not valid');
		
		$username->bind('123456');
		$this->assertTrue($strVal->validate($username->getValue(), $username), 'The validator is correctly valid');
		
		$strVal->min(null)->max(10);
		$username->bind('');
		$this->assertTrue($strVal->validate($username->getValue(), $username), 'The validator is correctly valid');
		
		$username->bind('1234567890');
		$this->assertTrue($strVal->validate($username->getValue(), $username), 'The validator is correctly valid');
		
		$username->bind('waaaaaaaayyyytooooolong');
		$this->assertFalse($strVal->validate($username->getValue(), $username), 'The validator is correctly not valid');
		
		/*
		 * test empty values are ok
		 */
		$errors = new phTestValidatable();
		$strVal->validate('', $errors);
		$this->assertEquals(0, sizeof($errors), 'Empty string passes ok');
		$strVal->validate(null, $errors);
		$this->assertEquals(0, sizeof($errors), 'Null passes ok');
	}
	
	public function testCompareValidator()
	{
		$password = new phFormDataItem('password');
		$confirmPassword = new phFormDataItem('confirmPassword');
		$confirmPassword->bind('password');
		
		$compareVal = new phCompareValidator($confirmPassword, phCompareValidator::EQUAL, array(
			phCompareValidator::INVALID=>"The passwords are not the same"
		));
		
		$this->assertFalse($compareVal->validate('notequal', $password), 'The validator is correctly not valid');
		$messages = $this->extractErrorMessages($password->getErrors());
		$this->assertTrue(in_array('The passwords are not the same', 
							$messages), 'error message set properly');
							
		$this->assertTrue($compareVal->validate('password', $password), 'The validator is correctly valid');
		
		$compareVal = new phCompareValidator($confirmPassword, phCompareValidator::NOT_EQUAL);
		$this->assertFalse($compareVal->validate('password', $password), 'The validator is correctly not valid');
		$this->assertTrue($compareVal->validate('notequal', $password), 'The validator is correctly valid');
		
		$confirmPassword->bind(5);
		
		$compareVal = new phCompareValidator($confirmPassword, phCompareValidator::LESS_THAN);
		$this->assertFalse($compareVal->validate(10, $password), 'The validator is correctly not valid');
		$this->assertTrue($compareVal->validate(1, $password), 'The validator is correctly valid');
		
		$compareVal = new phCompareValidator($confirmPassword, phCompareValidator::LESS_EQUAL);
		$this->assertFalse($compareVal->validate(10, $password), 'The validator is correctly not valid');
		$this->assertTrue($compareVal->validate(5, $password), 'The validator is correctly valid');
		
		$compareVal = new phCompareValidator($confirmPassword, phCompareValidator::GREATER_THAN);
		$this->assertFalse($compareVal->validate(1, $password), 'The validator is correctly not valid');
		$this->assertTrue($compareVal->validate(10, $password), 'The validator is correctly valid');
		
		$compareVal = new phCompareValidator($confirmPassword, phCompareValidator::GREATER_EQUAL);
		$this->assertFalse($compareVal->validate(1, $password), 'The validator is correctly not valid');
		$this->assertTrue($compareVal->validate(5, $password), 'The validator is correctly valid');
		
		/*
		 * test scalar values can be used
		 */
		$compareVal = new phCompareValidator(1, phCompareValidator::EQUAL);
		$this->assertFalse($compareVal->validate(2, $password), 'The validator is correctly not valid');
		$this->assertTrue($compareVal->validate(1, $password), 'The validator is correctly valid');
		
		/*
		 * test empty values don't equal
		 */
		$this->assertFalse($compareVal->validate('', $password), 'Empty string not equal');
		$this->assertFalse($compareVal->validate(null, $password), 'Null not equal');
	}
	
	/**
	 * Check the compare validator doesn't accept a non scalar
	 * @expectedException phValidatorException
	 */
	public function testCompareValidatorNonScalar()
	{
		$compareVal = new phCompareValidator(array('moo'), phCompareValidator::EQUAL);
	}
	
	public function testValidatorLogic()
	{
		$fail = new TestValidatorFail('fail!');
		$pass = new TestValidatorPass();
		$errors = new phTestValidatable();
		
		$logic = new phValidatorLogic($pass);
		$logic->and_($fail);
		$this->assertFalse($logic->validate('test', $errors), 'The and logic correctly fails');
		
		$logic = new phValidatorLogic($pass);
		$logic->or_($fail);
		$this->assertTrue($logic->validate('test', $errors), 'The or logic correctly passes');
		
		$logic = new phValidatorLogic($fail);
		$logic->or_($fail);
		$this->assertFalse($logic->validate('test', $errors), 'The or logic correctly fails');	
		
		$logic = new phValidatorLogic($pass);
		$logic->and_($fail)->or_($pass);
		$this->assertTrue($logic->validate('test', $errors), 'The v and v or v logic correctly passes');
		
		$logic = new phValidatorLogic($fail);
		$logic->and_($fail);
		$errors->resetErrors();
		$logic->validate('test', $errors);
		$this->assertEquals(sizeof($errors->getErrors()), 2, 'The 2 fails messages where added');
		
	}
	
	public function testUnboundFormFail()
	{
		$this->assertFalse($this->form->isValid(), 'Unbound form is not valid');
	}
	
	protected function extractErrorMessages($errors)
	{
		$messages = array();
		foreach($errors as $e)
		{
			$messages[] = $e->getMessage();
		}
		
		return $messages;
	}
}

class TestForm extends phForm
{
	/**
	 * @return phFormView
	 */
	public function getView()
	{
		return $this->_view;
	}
}

class TestValidatorFail implements phValidator
{
	const FAIL_CODE = 1;
	
	public function __construct($message)
	{
		$this->_message = $message;
	}
	
	public function validate($value, phValidatable $item)
	{
		$item->addError(new phValidatorError($this->_message, self::FAIL_CODE, $this));
		return false;
	}
}

class TestValidatorPass implements phValidator
{
	public function validate($value, phValidatable $item)
	{
		return true;
	}
}