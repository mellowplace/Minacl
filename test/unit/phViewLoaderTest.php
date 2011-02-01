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

class phTestFileViewLoader extends phFileViewLoader
{
	public function getFileAndDir($view)
	{
		return parent::getFileAndDir($view);
	}
}

class phViewLoaderTest extends phTestCase
{
	public function testFileViewLoader_getFileAndDir()
	{
		$fileLoader = new phTestFileViewLoader(realpath(dirname(__FILE__) . '/../resources'));
		
		$info = $fileLoader->getFileAndDir('login');
		$this->assertEquals('', $info['dir'], 'No dir for "login"');
		$this->assertEquals('login.php', $info['file'], 'File is login.php for "login"');
		
		$info = $fileLoader->getFileAndDir('user/login');
		$this->assertEquals('user', $info['dir'], 'Dir is user for "user/login"');
		$this->assertEquals('login.php', $info['file'], 'File is login.php for "user/login"');
		
		$info = $fileLoader->getFileAndDir('in/a/sub/view');
		$this->assertEquals('in/a/sub', $info['dir'], 'Dir is in/a/sub for "in/a/sub/view"');
		$this->assertEquals('view.php', $info['file'], 'File is view.php for "in/a/sub/view"');
	}
	
	public function testFileViewLoader_loadViewContents()
	{
		$fileLoader = new phTestFileViewLoader(realpath(dirname(__FILE__) . '/../resources'));

		/*
		 * attempt to load a view that does not exist
		 */
		$exceptionThrown = false;
		try 
		{
			$fileLoader->getViewFileOrStream('nonExistantView');			
		}
		catch(phFormException $e)
		{
			$exceptionThrown = true;
		}
		
		if(!$exceptionThrown)
		{
			$this->fail('A phFormException should have been thrown');
		}
		
		/*
		 * attempt to load an empty view
		 */
		$exceptionThrown = false;
		try 
		{
			$fileLoader->getViewFileOrStream('');			
		}
		catch(phFormException $e)
		{
			$exceptionThrown = true;
		}
		
		if(!$exceptionThrown)
		{
			$this->fail('A phFormException should have been thrown');
		}
		
		$loaded = false;
		require $fileLoader->getViewFileOrStream('fileViewLoaderTest');
		
		$this->assertTrue($loaded, 'The view was loaded');
	}
}