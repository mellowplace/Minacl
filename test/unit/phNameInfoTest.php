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

class phNameInfoTest extends phTestCase
{
	public function testParseName()
    {
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
    
    public function testArrayInfo()
    {
    	$info = new phArrayInfo('[name][first][]');
    	$this->assertTrue($info->isValid(), 'array keys are valid');
    	
    	$keys = $info->getKeys();
    	$this->assertEquals(sizeof($keys), 3, 'there are 3 array keys');
    	
    	$this->assertEquals($keys[0]->getKey(), 'name');
    	$this->assertFalse($keys[0]->isAutoKey(), 'name key is not an auto key');
    	$this->assertEquals($keys[1]->getKey(), 'first');
    	$this->assertFalse($keys[1]->isAutoKey(), 'first key is not an auto key');
    	$this->assertEquals($keys[2]->getKey(), '');
    	$this->assertTrue($keys[2]->isAutoKey(), 'last key is an auto key');
    }
    
    public function testArrayInfoGetKey()
    {
    	$info = new phArrayInfo('[name][first][]');
    	$this->assertEquals($info->getKeyInfo(0)->getKey(), 'name', 'Key at 0 returned correctly');
    	$this->assertEquals($info->getKeyInfo(1)->getKey(), 'first', 'Key at 1 returned correctly');
    	$this->assertTrue($info->getKeyInfo(2)->isAutoKey(), 'Key at 2 returned correctly');
    }
}