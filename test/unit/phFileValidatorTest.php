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
class phFileValidatorTest extends phTestCase
{
	/**
	 * @var phFileValidator
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
		$this->_validator = new phFileValidator();
	}
	
	public function testRequiredNoDataTriggers()
	{
		$this->_validator->setRequired(true);
		$this->_validator->validate(null, $this->_testValidatable);
		$errors = $this->_testValidatable->getErrors();
		$this->assertEquals(1, sizeof($errors), 'there is one error');
		$this->assertEquals(phFileValidator::REQUIRED, $errors[0]->getCode(), 'the error triggered is the required error');
	}
	
	public function testRequiredMissingFileTriggers()
	{
		$this->_validator->setRequired(true);
		$testData = $this->getTestData();
		$testData['tmp_name'] = '/file/does/not/exist/4r313r3343';
		
		$this->_validator->validate($testData, $this->_testValidatable);
		$errors = $this->_testValidatable->getErrors();
		$this->assertEquals(1, sizeof($errors), 'there is one error');
		$this->assertEquals(phFileValidator::REQUIRED, $errors[0]->getCode(), 'the error triggered is the required error');
	}
	
	public function testInvalidDataTriggersError()
	{
		$testData['invalid'] = 'bad file data';
		
		$this->_validator->validate($testData, $this->_testValidatable);
		$errors = $this->_testValidatable->getErrors();
		$this->assertEquals(1, sizeof($errors), 'there is one error');
		$this->assertEquals(phFileValidator::FILE_ERROR, $errors[0]->getCode(), 'the error triggered is the "file error"');
	}
	
	public function testRequiredPasses()
	{
		$testData = $this->getTestData();
		$testData['tmp_name'] = __FILE__; // file exists
		
		$this->_validator->setRequired(true);
		$this->_validator->validate($testData, $this->_testValidatable);
		
		$errors = $this->_testValidatable->getErrors();
		$this->assertEquals(0, sizeof($errors), 'there are no errors');
	}
	
	public function testFileErrorTriggersError()
	{
		$testData = $this->getTestData();
		$testData['tmp_name'] = __FILE__; // file exists
		$testData['error'] = 1; // but there is an error
		
		$this->_validator->validate($testData, $this->_testValidatable);
		$errors = $this->_testValidatable->getErrors();
		$this->assertEquals(1, sizeof($errors), 'there is one error');
		$this->assertEquals(phFileValidator::FILE_ERROR, $errors[0]->getCode(), 'the error triggered is the "file error"');
		$this->assertTrue(strstr($errors[0]->getMessage(), 'The uploaded file exceeds the upload_max_filesize directive in php.ini')!==false, 'The PHP error message was written in to the error');
	}
	
	public function testMimeType()
	{
		$this->_validator->addRequiredMimeType('text/html')->addRequiredMimeType('text/plain');
		
		$testData = $this->getTestData();
		$testData['tmp_name'] = __FILE__;
		$testData['type'] = 'text/plain';
		
		$this->_validator->validate($testData, $this->_testValidatable);
		$errors = $this->_testValidatable->getErrors();
		$this->assertEquals(0, sizeof($errors), 'there are no errors for a valid mime type');
		
		$testData['type'] = 'image/gif';
		$this->_validator->validate($testData, $this->_testValidatable);
		$errors = $this->_testValidatable->getErrors();
		$this->assertEquals(1, sizeof($errors), 'there is 1 error for an invalid mime type');
		$this->assertEquals(phFileValidator::INVALID_MIME_TYPE, $errors[0]->getCode(), 'the error triggered is the invalid mime error');
		$this->assertTrue(strstr($errors[0]->getMessage(), 'valid types are: text/html, text/plain')!==false, 'The valid types were written into the error');
	}
		
	protected function getTestData()
	{
		return array(
			'name' => 'c:\my documents\file.html',
			'type' => 'text/html',
			'size' => 10,
			'tmp_name' => '/tmp/phpFile0123',
			'error' => 0
		);
	}
}