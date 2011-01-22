<?php
/*
 * phForms Project: An HTML forms library for PHP
 *          https://github.com/mellowplace/PHP-HTML-Driven-Forms/
 * Copyright (c) 2010, 2011 Rob Graham
 * 
 * This file is part of phForms.
 *
 * phForms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version.
 *
 * phForms is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public 
 * License along with phForms.  If not, see 
 * <http://www.gnu.org/licenses/>.
 */

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