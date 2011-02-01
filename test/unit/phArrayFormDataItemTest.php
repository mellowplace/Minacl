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

require_once 'phBaseArrayFormDataItemTest.php';
require_once realpath(dirname(__FILE__)) . '/../resources/phTestForm.php';
 
class phArrayFormDataItemTest extends phBaseArrayFormDataItemTest
{	
	/**
     * @expectedException phFormException
     */
	public function testAlreadyRegisteredArrayKeys()
	{
		$testData = new phArrayFormDataItem('test');
		
		$info = new phArrayInfo('[1]');
		$data = new phFormDataItem('1');
		$testData->registerArrayKey($info->getKeyInfo(0), $data);
		$testData->registerArrayKey($info->getKeyInfo(0), $data); // should throw exception as already registered
	}
	
	/**
	 * Test a normal key then an auto key fails
     * @expectedException phFormException
     */
	public function testRegisterMixedArrayKeys()
	{
		$testData = new phArrayFormDataItem('test');
		
		$info = new phArrayKeyInfo('1', false, phArrayKeyInfo::NUMERIC);
		$testData->registerArrayKey($info, $testData);
		
		$info = new phArrayKeyInfo('', true, phArrayKeyInfo::NUMERIC);
		$testData->registerArrayKey($info, $testData); // should not be able to have auto key and specified!
	}
	
	/**
	 * Test an auto key first then a normal key fails
	 * 
     * @expectedException phFormException
     */
	public function testRegisterMixedArrayKeysReverse()
	{
		$testData = new phArrayFormDataItem('test');
		
		$info = new phArrayKeyInfo('', true, phArrayKeyInfo::NUMERIC);
		$testData->registerArrayKey($info, $testData); // should not be able to have auto key and specified!
		
		$info = new phArrayKeyInfo('1', false, phArrayKeyInfo::NUMERIC);
		$testData->registerArrayKey($info, $testData);
	}
	
	public function testRegisterAutoKeys()
	{
		$testData = new phArrayFormDataItem('test');
		
		$info = new phArrayKeyInfo('', true, phArrayKeyInfo::NUMERIC);
		$testData->registerArrayKey($info, new phFormDataItem('test')); // 0
		$testData->registerArrayKey($info, new phFormDataItem('test')); // 1
		$testData->registerArrayKey($info, new phFormDataItem('test')); // 2
		/*
		 * test a different type
		 */
		$fileType = new phFileFormDataItem('test');
		$testData->registerArrayKey($info, $fileType); // 3
		
		$this->assertEquals(sizeof($testData), 4, 'Test data has 4 elements');
		$this->assertTrue($testData[0] instanceof phFormDataItem, 'There is data at [0]');
		$this->assertTrue($testData[1] instanceof phFormDataItem, 'There is data at [1]');
		$this->assertTrue($testData[2] instanceof phFormDataItem, 'There is data at [2]');
		$this->assertSame($testData[3], $fileType, 'There is data at [3]');
	}
	
	public function testBindData()
	{
		$testData = new phArrayFormDataItem('test');
		
		$info = new phArrayKeyInfo(0, false, phArrayKeyInfo::NUMERIC);
		$testData->registerArrayKey($info, new phFormDataItem('test')); 
		$info = new phArrayKeyInfo(1, false, phArrayKeyInfo::NUMERIC);
		$testData->registerArrayKey($info, new phFormDataItem('test')); 
		
		$testData->bind(array('test', 'data'));
		$this->assertEquals($testData[0]->getValue(), 'test', 'Data at [0] is "test"');
		$this->assertEquals($testData[1]->getValue(), 'data', 'Data at [1] is "data"');
		
		$testData = new phArrayFormDataItem('test');
		$info = new phArrayKeyInfo('city', false, phArrayKeyInfo::STRING);
		$testData->registerArrayKey($info, new phFormDataItem('test')); 
		$info = new phArrayKeyInfo('zip', false, phArrayKeyInfo::STRING);
		$testData->registerArrayKey($info, new phFormDataItem('test')); 
				
		$testData->bind(array(
				'city' => 'London',
				'zip' => 90210
		));
		
		$this->assertEquals('London', $testData['city']->getValue(), 'Data at [city] is good');
		$this->assertEquals(90210, $testData['zip']->getValue(), 'Data at [zip] is good');
		/*
		 * test that not passing some data still ends with it being set to null
		 */
		$testData = new phArrayFormDataItem('test');
		$info = new phArrayKeyInfo('city', false, phArrayKeyInfo::STRING);
		$testData->registerArrayKey($info, new phFormDataItem('test')); 
		$info = new phArrayKeyInfo('zip', false, phArrayKeyInfo::STRING);
		$testData->registerArrayKey($info, new phFormDataItem('test')); 
		$info = new phArrayKeyInfo('first_name', false, phArrayKeyInfo::STRING);
		$testData->registerArrayKey($info, new phFormDataItem('test'));
		
		$testData->bind(array(
				'zip' => 90210
		));
		
		$this->assertEquals(null, $testData['city']->getValue(), 'Data at [address][city] is good');
		$this->assertEquals(90210, $testData['zip']->getValue(), 'Data at [address][zip] is good');
		$this->assertEquals($testData['first_name']->getValue(), null, 'No first_name data bound');
	}
	
	/**
	 * Tests that when the data is not posted back - i.e. with a check box with a name like ids[0] that
	 * hasn't been checked - it still calls bind on that item
	 */
	public function testBindForCheckboxes()
	{
		$testData = new phArrayFormDataItem('test');
		// add test[0]
		$info = new phArrayKeyInfo(0, false, phArrayKeyInfo::NUMERIC);
		$data0 = new phTestFormDataItem(0);
		$testData->registerArrayKey($info, $data0); 
		// add test[1]
		$info = new phArrayKeyInfo(1, false, phArrayKeyInfo::NUMERIC);
		$data1 = new phTestFormDataItem(1);
		$testData->registerArrayKey($info, $data1);
		// bind only 0
		$testData->bind(array(0=>'test'));
		// make sure both where bound
		$this->assertTrue($data0->wasBound, 'data0 was bound');
		$this->assertEquals($data0->getValue(), 'test', 'data0 was bound with test');
		$this->assertTrue($data1->wasBound, 'data1 was bound');
		$this->assertEquals($data1->getValue(), null, 'data1 was bound with test');
		
		/*
		 * test 2 levels of array
		 * 
		 * i.e...
		 * 
		 * test[ids][0]
		 * test[ids][1]
		 */
		$testData = new phArrayFormDataItem('test');
		$info = new phArrayKeyInfo('ids', false, phArrayKeyInfo::STRING);
		$arrayData = new phArrayFormDataItem('ids');
		$testData->registerArrayKey($info, $arrayData);
		// add test[ids][0]
		$info = new phArrayKeyInfo(0, false, phArrayKeyInfo::NUMERIC);
		$data0 = new phTestFormDataItem(0);
		$arrayData->registerArrayKey($info, $data0);
		// add test[ids][1]
		$info = new phArrayKeyInfo(1, false, phArrayKeyInfo::NUMERIC);
		$data1 = new phTestFormDataItem(1);
		$arrayData->registerArrayKey($info, $data1);
		// bind nothing
		$testData->bind(array());
		// make sure both where bound
		$this->assertTrue($data0->wasBound, 'data0 was bound');
		$this->assertEquals($data0->getValue(), null, 'data0 was bound with test');
		$this->assertTrue($data1->wasBound, 'data1 was bound');
		$this->assertEquals($data1->getValue(), null, 'data1 was bound with test');
	}
	
	/**
     * @expectedException phFormException
     */
	public function testBindInvalidData()
	{
		$testData = new phArrayFormDataItem('test');
		$info = new phArrayKeyInfo(0, false, phArrayKeyInfo::NUMERIC);
		$testData->registerArrayKey($info, new phFormDataItem('test')); 
		$info = new phArrayKeyInfo(1, false, phArrayKeyInfo::NUMERIC);
		$testData->registerArrayKey($info, new phFormDataItem('test')); 
		
		$testData->bind(array('test', 'data', '1 too many')); // too much data
	}
	
	/**
     * @expectedException phFormException
     */
	public function testBindInvalidScalarData()
	{
		$testData = new phArrayFormDataItem('test');
		$info = new phArrayKeyInfo(0, false, phArrayKeyInfo::NUMERIC);
		$testData->registerArrayKey($info, new phFormDataItem('test')); 
		$info = new phArrayKeyInfo(1, false, phArrayKeyInfo::NUMERIC);
		$testData->registerArrayKey($info, new phFormDataItem('test')); 
		
		$testData->bind('test'); // wrong data type, should be an array
	}
	
	/**
	 * tests the auto key calcs properly
	 */
	public function testAutoKeys()
	{
		$testData = new phArrayFormDataItem('test');
		$this->assertEquals($testData->getNextAutoKey(), 0, 'auto key before registering anything is 0');
		$info = new phArrayKeyInfo('', true, phArrayKeyInfo::NUMERIC);
		$testData->registerArrayKey($info, new phFormDataItem('test'));
		$this->assertEquals($testData->getNextAutoKey(), 1, 'auto key after one register is 1');
		$info = new phArrayKeyInfo('', true, phArrayKeyInfo::NUMERIC);
		$testData->registerArrayKey($info, new phFormDataItem('test'));
		$this->assertEquals($testData->getNextAutoKey(), 2, 'auto key after two register is 2');
	}
	
	protected function createArrayDataItem($name)
	{
		return new phArrayFormDataItem($name);
	}
}

class phTestFormDataItem extends phFormDataItem
{
	public $wasBound = false;
	
	public function bind($value)
	{
		$this->wasBound = true;
		parent::bind($value);
	}
}