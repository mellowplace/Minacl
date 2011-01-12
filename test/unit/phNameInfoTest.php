<?php
require_once 'phTestCase.php';
require_once realpath(dirname(__FILE__)) . '/../../lib/form/phLoader.php';
phLoader::registerAutoloader();

class phNameInfoTest extends phTestCase
{
	public function testParseName()
    {
    	$view = new phFormViewTest('arrayTestView', new phForm('test', 'arrayTestView'));
    	
    	$info = new phNameInfo('ids[1]');
    	$this->assertTrue($info->isValid(), 'ids[1] is valid');
    	$this->assertTrue($info->isArray(), 'ids[1] has been identified as an array');
    	$this->assertEquals($info->getArrayKeyString(), '[1]', 'Array parts is [1]');
    	$this->assertEquals($info->getName(), 'ids', 'Name is ids');
    	
    	$info = new phNameInfo('ids[]');
    	$this->assertTrue($info->isValid(), 'ids[] is valid');
    	$this->assertTrue($info->isArray(), 'ids[] has been identified as an array');
    	$this->assertEquals($info->getArrayKeyString(), '[]', 'Array parts is []');
    	$this->assertEquals($info->getName(), 'ids', 'Name is ids');
    	
    	$info = new phNameInfo('ids[1][moo]');
    	$this->assertTrue($info->isValid(), 'ids[1][moo] is valid');
    	$this->assertTrue($info->isArray(), 'ids[1][moo] has been identified as an array');
    	$this->assertEquals($info->getArrayKeyString(), '[1][moo]', 'Array parts is [1][moo]');
    	$this->assertEquals($info->getName(), 'ids', 'Name is ids');
    }
}