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

 /**
 * Tests for the phFileFormDataItem class
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage test
 */
class phFileFormDataItemTest extends phTestCase
{
	/**
	 * @var phFileFormDataItem
	 */
	protected $fileData = null;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->fileData = new phFileFormDataItem('testFile');
	}
	
	/**
     * @expectedException phFileDataException
     */
	public function testBadBindThrowsException()
	{
		$this->fileData->bind('not a file array');
	}
	
	/**
     * @expectedException phFileDataException
     */
	public function testBadBindThrowsException2()
	{
		// incomplete file array, no tmp_name and error
		$this->fileData->bind(array(
			'name' => 'name',
			'type' => 'text/html',
			'size' => 10
		));
	}
	
	/**
     * @expectedException phFileDataException
     */
	public function testNonExistantFileThrowsException()
	{
		$this->fileData->bind($this->getTestData());
		$this->fileData->saveFile(sys_get_temp_dir() . '/tempFile.html');
	}
	
	public function testBindAndUtilityMethods()
	{
		$this->fileData->bind($this->getTestData());
		
		$this->assertFalse($this->fileData->hasError(), 'no errors in the file');
		$this->assertEquals(10, $this->fileData->getFileSize(), 'file size is 10 bytes');
		$this->assertEquals('text/html', $this->fileData->getMimeType(), 'mime type is text/html');
		$this->assertEquals('/tmp/phpFile0123', $this->fileData->getTempFileName(), 'tmp file name is correct');
		$this->assertEquals('c:\my documents\file.html', $this->fileData->getOriginalFileName(), 'original name is correct');
	}
	
	public function testFormHandlesFileElement()
	{
		$form = new phForm('test', 'fileUploadTestView');
		$this->assertTrue($form->uploadFile instanceof phFileFormDataItem, 'uploadFile is a phFileFormDataItem');
	}
	
	private function getTestData()
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
