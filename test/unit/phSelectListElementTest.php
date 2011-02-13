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

require_once 'phAbstractSelectListTest.php';

/**
 * Tests the phSelectListElement class
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage test
 */
class phSelectListElementTest extends phAbstractSelectListTest
{
	public function testIsMultiple()
	{
		$single = $this->createSelectListElement('test', array('test'=>'test', 'test2'=>'test2'));
		$this->assertFalse($single->isMultiple(), 'select list is a single select');
		$multiple = $this->createSelectListElement('test', array('test'=>'test', 'test2'=>'test2'), true);
		$this->assertTrue($multiple->isMultiple(), 'select list is a multiple select');
	}
	
	public function testSingleSelectSetValue()
	{
		$single = $this->createSelectListElement('test', array('test'=>'test', 'test2'=>'test2'));
		$single->setValue('test');
		// assert the first option is selected
		$elements = $single->getElement()->xpath("//option[@value='test']");
		$this->assertEquals(1, sizeof($elements), 'found the "test" option');
		$e = $elements[0];
		$this->assertEquals('selected', (string)$e->attributes()->selected, 'the "test" option is selected');
		// assert the second option is not selected
		$elements = $single->getElement()->xpath("//option[@value='test2']");
		$this->assertEquals(1, sizeof($elements), 'found the "test2" option');
		$e = $elements[0];
		$this->assertFalse(isset($e->attributes()->selected), 'the "test2" option is NOT selected');
		// reset the value to test2 and check test isn't selected and test2 *is*
		$single->setValue('test2');
		// assert the second option is now selected
		$elements = $single->getElement()->xpath("//option[@value='test2']");
		$this->assertEquals(1, sizeof($elements), 'found the "test2" option');
		$e = $elements[0];
		$this->assertEquals('selected', (string)$e->attributes()->selected, 'the "test2" option is selected');
		// assert the first option is no longer selected
		$elements = $single->getElement()->xpath("//option[@value='test']");
		$this->assertEquals(1, sizeof($elements), 'found the "test" option');
		$e = $elements[0];
		$this->assertFalse(isset($e->attributes()->selected), 'the "test" option is NOT selected');
	}
	
	public function testMultipleSelectSetValue()
	{
		$multiple = $this->createSelectListElement('test', array('test'=>'test', 'test2'=>'test2'), true);
		$multiple->setValue(array('test','test2'));
		// assert both options are selected
		$elements = $multiple->getElement()->xpath("//option");
		$this->assertEquals(2, sizeof($elements), 'found both options');
		foreach($elements as $e)
		{
			$this->assertEquals('selected', (string)$e->attributes()->selected, 'the "' . (string)$e->attributes()->value . '" option is selected');
		}
		
		$multiple->setValue(array());
		// assert both options are not selected
		$elements = $multiple->getElement()->xpath("//option");
		$this->assertEquals(2, sizeof($elements), 'found both options');
		foreach($elements as $e)
		{
			$this->assertFalse(isset($e->attributes()->selected), 'the "' . (string)$e->attributes()->value . '" option is *not* selected');
		}
		
		$multiple->setValue(array('test'));
		// assert one option is selected
		$elements = $multiple->getElement()->xpath("//option[@selected='selected']");
		$this->assertEquals(1, sizeof($elements), 'one option selected');
	}
	
	
	public function testGetRawValue()
	{
		$single = $this->createSelectListElement('test', array('test'=>'test', 'test2'=>'test2'));
		$this->assertEquals(null, $single->getRawValue(), 'when nothing is selected in a single list the value is null');
		
		$single = $this->createSelectListElement('test', array('test'=>'test', 'test2'=>'test2'), false, array('test'));
		$this->assertEquals('test', $single->getRawValue(), 'when one thing is selected the value is test');
		
		$multiple = $this->createSelectListElement('test', array('test'=>'test', 'test2'=>'test2'), true);
		$this->assertEquals(array(), $multiple->getRawValue(), 'when nothing is selected in a multiple list the value is array()');
		
		$multiple = $this->createSelectListElement('test', array('test'=>'test', 'test2'=>'test2'), true, array('test', 'test2'));
		$this->assertEquals(array('test', 'test2'), $multiple->getRawValue(), 'when both options are selected in a multiple list the value is array(test, test2)');
		
		$multiple = $this->createSelectListElement('test', array('test'=>'test', 'test2'=>'test2'), true, array('test2'));
		$this->assertEquals(array('test2'), $multiple->getRawValue(), 'when one options is selected in a multiple list the value is array(test2)');
	}
	
	/**
	 * Test that when a select is a single select and you try to set its value with
	 * an array that an exception is thrown
	 * @expectedException phFormException
	 */
	public function testSingleSetValueWithArray()
	{
		$single = $this->createSelectListElement('test', array('test'=>'test', 'test2'=>'test2'));
		$single->setValue(array('test', 'test2'));
	}
	
	/**
	 * Test that when a select is a multi select and you try to set its value with
	 * a scalar that an exception is thrown
	 * @expectedException phFormException
	 */
	public function testMultipleSetValueWithScalar()
	{
		$single = $this->createSelectListElement('test', array('test'=>'test', 'test2'=>'test2'), true);
		$single->setValue('test2');
	}
}