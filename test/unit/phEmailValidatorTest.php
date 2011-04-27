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
 * Tests for the email validator
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage test
 */
class phEmailValidatorTest extends phTestCase
{
	/**
	 * @var phEmailValidator
	 */
	protected $validator = null;
	/**
	 * @var phTestValidatable
	 */
	protected $errors = null;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->validator = new phEmailValidator();
		$this->errors = new phTestValidatable();
	}
	
	/**
	 * Test various valid combinations of email addies
	 */
	public function testValidEmails()
	{
		$emails = array(
			'rob.graham@example.org',
			'robgraham@example.co.uk',
			'1234@example.org',
			'rob@[127.0.0.1]',
			'rob!@example.org',
			'!#$%&\'*+-/=?^_`|}~@example.org', // all the specials
			'"rob@"@example.org' // quoted non dot-atom text is ok			
		);
		
		foreach($emails as $e)
		{
			$this->assertTrue($this->validator->validate($e, $this->errors), "Email addie {$e} is correctly valid");
		}
	}
	
	/**
	 * Test that an empty email validates ok.
	 */
	public function testEmptyEmail()
	{
		$this->assertTrue($this->validator->validate('', $this->errors), "Email addie {$e} is correctly valid");
	}
	
	public function testInvalidEmails()
	{
		$emails = array(
			'rob@localhost', // public emails only
			'me(addie@example.com', // this and the following all have special chars outside of quotes
			'me[addie@example.com',
			'me]addie@example.com',
			'me\addie@example.com',
			'me;addie@example.com',
			'me:addie@example.com',
			'me,addie@example.com',
			'me<addie@example.com',
			'me>addie@example.com',
			'Abc.example.com', // no @ sign
			'A@b@c@example.com', // @ signs out of quotes - only 1 is allowed
			'shouldbewaytoolong@biiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiigggggggggggggdomaiiiiiiiiinnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn.com',
			'localpartistoolonglocalpartistoolonglocalpartistoolonglocalpartistool@example.org', //local part exceeds 64 chars
			'Pelé@example.com', // i18n email that is not RFC 5321/2 compat
			'rob.@example.org', // bad dot-atom (text must follow the .)
			'.rob@example.org', // bad dot-atom (cannot start with a .)
			'rob@127.0.0.1', // ip's must be in square brackets
			'rob..graham@example.org', // bad dot-atom (consequetive dots not allowed)
			"rob@example.org\nrob@127.0.0.1" // make sure you cannot validate the email by using a new line
		);
		
		foreach($emails as $e)
		{
			$this->assertFalse($this->validator->validate($e, $this->errors), "Email addie {$e} is correctly invalid");
		}
	}
}