<?php
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