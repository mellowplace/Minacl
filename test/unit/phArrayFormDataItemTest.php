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
require_once dirname(__FILE__) . '/../../lib/form/phLoader.php';
phLoader::registerAutoloader();

require_once realpath(dirname(__FILE__)) . '/../resources/phTestForm.php';
 
class phArrayFormDataItemTest extends phTestCase
{
	public function testExtractArrayKeys()
	{
		$testData = new phTestArrayFormDataItem('test');
		
		$keys = $testData->extractArrayKeys('[1][2]');
		
		$keys = $testData->extractArrayKeys('[]');
		$this->assertTrue(is_array($keys), 'returned keys is an array');
		$this->assertEquals(sizeof($keys), 1, 'keys has 1 element');
		$this->assertEquals($keys[0], '', 'key for [] is an empty string');
		
		$keys = $testData->extractArrayKeys('[1]');
		$this->assertTrue(is_array($keys), 'returned keys is an array');
		$this->assertEquals(sizeof($keys), 1, 'keys has 1 element');
		$this->assertEquals($keys[0], 1, 'key for [1] is 1');
		
		$keys = $testData->extractArrayKeys('[moo]');
		$this->assertTrue(is_array($keys), 'returned keys is an array');
		$this->assertEquals(sizeof($keys), 1, 'keys has 1 element');
		$this->assertEquals($keys[0], 'moo', 'key for [moo] is moo');
		
		$keys = $testData->extractArrayKeys('[1][2]');
		$this->assertTrue(is_array($keys), 'returned keys is an array');
		$this->assertEquals(sizeof($keys), 2, 'keys has 2 elements');
		$this->assertEquals($keys[0], 1, 'key for [1] is 1');
		$this->assertEquals($keys[1], 2, 'key for [2] is 2');
	}
	
	/**
     * @expectedException phFormException
     */
	public function testAlreadyRegisteredArrayKeys()
	{
		$testData = new phArrayFormDataItem('test');
		
		$testData->registerArrayKeyString('[1]');
		$testData->registerArrayKeyString('[1]'); // should throw exception as already registered
	}
	
	/**
     * @expectedException phFormException
     */
	public function testRegisterMixedArrayKeys()
	{
		$testData = new phArrayFormDataItem('test');
		
		$testData->registerArrayKeyString('[]');
		$testData->registerArrayKeyString('[1]'); // should not be able to have auto key and specified!
	}
	
	/**
     * @expectedException phFormException
     */
	public function testRegisterMixedArrayKeysAtSecondLevel()
	{
		$testData = new phTestArrayFormDataItem('test');
		
		$testData->registerArrayKeyString('[moo][]');
		$testData->registerArrayKeyString('[moo][test]'); // should not be able to have auto key and specified!
	}
	
	public function testRegisterAutoKeys()
	{
		$testData = new phTestArrayFormDataItem('test');
		
		$testData->registerArrayKeyString('[]'); // 0
		$testData->registerArrayKeyString('[]'); // 1
		$testData->registerArrayKeyString('[]'); // 2
		$testData->registerArrayKeyString('[]'); // 3
		
		$this->assertEquals(sizeof($testData->_arrayTemplate), 4, 'Test data has 4 elements');
		$this->assertTrue($testData->_arrayTemplate[0] instanceof phFormDataItem, 'There is data at [0]');
		$this->assertTrue($testData->_arrayTemplate[1] instanceof phFormDataItem, 'There is data at [1]');
		$this->assertTrue($testData->_arrayTemplate[2] instanceof phFormDataItem, 'There is data at [2]');
		$this->assertTrue($testData->_arrayTemplate[3] instanceof phFormDataItem, 'There is data at [3]');
	}
	
	public function testIsArrayKeysUnregistered()
	{
		$testData = new phTestArrayFormDataItem('test');
		
		$testData->_arrayTemplate = array(0=>1);
		$this->assertFalse($testData->isArrayKeysUnregistered(array(0=>1)), '[0] already registered');
		
		$testData->_arrayTemplate = array(1=>1);
		$this->assertFalse($testData->isArrayKeysUnregistered(array(1=>1)), '[1] already registered');
		
		$testData->_arrayTemplate = array(0=>array(0=>1));
		$this->assertFalse($testData->isArrayKeysUnregistered(array(0=>array(0=>1))), '[0][0] already registered');
		
		$testData->_arrayTemplate = array(0=>1);
		$this->assertTrue($testData->isArrayKeysUnregistered(array(1=>1)), '[1] not registered');
		
		$testData->_arrayTemplate = array(0=>array(0=>1));
		$this->assertTrue($testData->isArrayKeysUnregistered(array(0=>array(1=>1))), '[0][1] not registered');
	}
	
	public function testArraysStoredProperly()
	{
		$testData = new phTestArrayFormDataItem('test');
		$testData->registerArrayKeyString('[0]');
		$testData->registerArrayKeyString('[1]');
		$this->assertTrue($testData->_arrayTemplate[0] instanceof phFormDataItem, 'Registered key [0] stored properly');
		$this->assertTrue($testData->_arrayTemplate[1] instanceof phFormDataItem, '2nd registered key [1] stored properly');
		
		$testData = new phTestArrayFormDataItem('test');
		$testData->registerArrayKeyString('[1]');
		$this->assertTrue($testData->_arrayTemplate[1] instanceof phFormDataItem, 'Registered key [1] stored properly');
		
		$testData->registerArrayKeyString('[2]');
		$this->assertTrue($testData->_arrayTemplate[2] instanceof phFormDataItem, '2nd registered key [2] stored properly');
		
		$testData = new phTestArrayFormDataItem('moo');
		$testData->registerArrayKeyString('[moo]');
		$this->assertTrue($testData->_arrayTemplate['moo'] instanceof phFormDataItem, 'Registered key [moo] stored properly');
		
		$testData = new phTestArrayFormDataItem('test');
		$testData->registerArrayKeyString('[data][1]');
		$testData->registerArrayKeyString('[data][2]');
		$this->assertTrue($testData->_arrayTemplate['data'][1] instanceof phFormDataItem, 'Registered key [data][1] stored properly');
		$this->assertTrue($testData->_arrayTemplate['data'][2] instanceof phFormDataItem, '2nd registered key [data][2] stored properly');
	}
	
	public function testBindData()
	{
		$testData = new phArrayFormDataItem('test');
		$testData->registerArrayKeyString('[0]');
		$testData->registerArrayKeyString('[1]');
		
		$testData->bind(array('test', 'data'));
		$this->assertEquals($testData[0]->getValue(), 'test', 'Data at [0] is "test"');
		$this->assertEquals($testData[1]->getValue(), 'data', 'Data at [1] is "data"');
		
		$testData = new phArrayFormDataItem('test');
		$testData->registerArrayKeyString('[address][city]');
		$testData->registerArrayKeyString('[address][zip]');
		
		$testData->bind(array(
			'address' => array (
				'city' => 'London',
				'zip' => 90210
			)
		));
		
		$this->assertEquals('London', $testData['address']['city']->getValue(), 'Data at [address][city] is good');
		$this->assertEquals(90210, $testData['address']['zip']->getValue(), 'Data at [address][zip] is good');
		/*
		 * test that not passing some data still ends with it being set to null
		 */
		$testData = new phArrayFormDataItem('test');
		$testData->registerArrayKeyString('[address][city]');
		$testData->registerArrayKeyString('[address][zip]');
		$testData->registerArrayKeyString('[first_name]');
		
		$testData->bind(array(
			'address' => array (
				'zip' => 90210
			)
		));
		
		$this->assertEquals(null, $testData['address']['city']->getValue(), 'Data at [address][city] is good');
		$this->assertEquals(90210, $testData['address']['zip']->getValue(), 'Data at [address][zip] is good');
		$this->assertEquals($testData['first_name']->getValue(), null, 'No first_name data bound');
	}
	
	/**
     * @expectedException phFormException
     */
	public function testBindInvalidData()
	{
		$testData = new phArrayFormDataItem('test');
		$testData->registerArrayKeyString('[0]');
		$testData->registerArrayKeyString('[1]');
		
		$testData->bind(array('test', 'data', '1 too many')); // too much data
	}
	
	/**
     * @expectedException phFormException
     */
	public function testBindInvalidMultiDimData()
	{
		$testData = new phArrayFormDataItem('test');
		$testData->registerArrayKeyString('[address][city]');
		$testData->registerArrayKeyString('[address][zip]');
		
		$testData->bind(array(
			'address' => array (
				'city' => 'London',
				'zip' => 90210,
				'unknown' => true // invalid data - wasn't registered
			)
		));
	}
	
	/**
     * @expectedException phFormException
     */
	public function testBindInvalidScalarData()
	{
		$testData = new phArrayFormDataItem('test');
		$testData->registerArrayKeyString('[0]');
		$testData->registerArrayKeyString('[1]');
		
		$testData->bind('test'); // wrong data type, should be an array
	}
}

class phTestArrayFormDataItem extends phArrayFormDataItem
{
	public $_arrayTemplate = array();
	
	public function extractArrayKeys($keyString)
	{
		return parent::extractArrayKeys($keyString);
	}
	
	public function isArrayKeysUnregistered($keys, $currentKeys = null, $currentRegistered = null)
	{
		return parent::isArrayKeysUnregistered($keys, $currentKeys, $currentRegistered);
	}
}