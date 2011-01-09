<?php
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
		$this->assertEquals($testData->_arrayTemplate[0], 1, 'There is data at [0]');
		$this->assertEquals($testData->_arrayTemplate[1], 1, 'There is data at [1]');
		$this->assertEquals($testData->_arrayTemplate[2], 1, 'There is data at [2]');
		$this->assertEquals($testData->_arrayTemplate[3], 1, 'There is data at [3]');
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
		$this->assertEquals($testData->_arrayTemplate[0], 1, 'Registered key [0] stored properly');
		$this->assertEquals($testData->_arrayTemplate[1], 1, '2nd registered key [1] stored properly');
		
		$testData = new phTestArrayFormDataItem('test');
		$testData->registerArrayKeyString('[1]');
		$this->assertEquals($testData->_arrayTemplate[1], 1, 'Registered key [1] stored properly');
		
		$testData->registerArrayKeyString('[2]');
		$this->assertEquals($testData->_arrayTemplate[2], 1, '2nd registered key [2] stored properly');
		
		$testData = new phTestArrayFormDataItem('moo');
		$testData->registerArrayKeyString('[moo]');
		$this->assertEquals($testData->_arrayTemplate['moo'], 1, 'Registered key [moo] stored properly');
		
		$testData = new phTestArrayFormDataItem('test');
		$testData->registerArrayKeyString('[data][1]');
		$testData->registerArrayKeyString('[data][2]');
		$this->assertEquals($testData->_arrayTemplate['data'][1], 1, 'Registered key [data][1] stored properly');
		$this->assertEquals($testData->_arrayTemplate['data'][2], 1, '2nd registered key [data][2] stored properly');
	}
	
	public function testBindData()
	{
		$testData = new phArrayFormDataItem('test');
		$testData->registerArrayKeyString('[0]');
		$testData->registerArrayKeyString('[1]');
		
		$testData->bind(array('test', 'data'));
		$this->assertEquals($testData[0], 'test', 'Data at [0] is "test"');
		$this->assertEquals($testData[1], 'data', 'Data at [1] is "data"');
		
		$testData = new phArrayFormDataItem('test');
		$testData->registerArrayKeyString('[address][city]');
		$testData->registerArrayKeyString('[address][zip]');
		
		$testData->bind(array(
			'address' => array (
				'city' => 'London',
				'zip' => 90210
			)
		));
		$this->assertEquals($testData['address'], array('city' => 'London', 'zip' => 90210), 'Data at [address] is good');
		
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
		
		$this->assertEquals($testData['address'], array('city' => null, 'zip' => 90210), 'Data at [address] is good');
		$this->assertEquals($testData['first_name'], null, 'No first_name data bound');
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