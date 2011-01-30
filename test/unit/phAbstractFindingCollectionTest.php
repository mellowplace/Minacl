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

/**
 * Tests the phAbstractFindingCollection class
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage test
 */
class phAbstractFindingCollectionTest extends phTestCase
{
	public function testRecurseArray()
	{
		$collection = new phTestAbstractFindingCollection();
		
		/*
		 * make an array address[city]
		 */
		$array = new phArrayFormDataItem('address');
		$key = new phArrayKeyInfo('city', false, phArrayKeyInfo::STRING);
		$item = new phFormDataItem('city');
		/*
		 * register it...
		 */
		$array->registerArrayKey($key, $item);
		
		$collection->addData('address', $item);
		
		$gottenItem = $collection->recurseArray($array, array($key));
		
		$this->assertSame($gottenItem, $item, 'recurseArray found the item');
	}
	
	public function testFind()
	{
		$collection = new phTestAbstractFindingCollection();
		
		/*
		 * make and register test[address]
		 */
		$array = new phArrayFormDataItem('test');
		$key = new phArrayKeyInfo('address', false, phArrayKeyInfo::STRING);
		$item1 = new phFormDataItem('address');
		$array->registerArrayKey($key, $item1);
		
		$collection->addData('test', $array);
		/*
		 * make and register ids[]
		 */
		$array = new phArrayFormDataItem('ids');
		$key = new phArrayKeyInfo('', true, phArrayKeyInfo::NUMERIC);
		$item2 = new phFormDataItem('0');
		$array->registerArrayKey($key, $item2);
		
		$collection->addData('ids', $array);
		
		$this->assertSame($item1, $collection->find('test[address]'));
		$this->assertSame($item2, $collection->find('ids[0]'));
		$this->assertTrue($collection->find('ids') instanceof phArrayFormDataItem, 'ids is an array data type');
	}
	
	/**
	 * @expectedException phFormException
	 */
	public function testAmbiguousFind()
	{
		$collection = new phTestAbstractFindingCollection();
		$collection->find('ids[]');
	}
	
	/**
	 * @expectedException phFormException
	 */
	public function testAmbiguousFind2()
	{
		$collection = new phTestAbstractFindingCollection();
		$collection->find('address[city][]');
	}
	
	public function testFindArrayWhenDataTypeIsNotAnArray()
	{
		$collection = new phTestAbstractFindingCollection();
		$collection->addData('test', new phFormDataItem('test'));
		
		$this->assertEquals(null, $collection->find('test[0]'), 'find returns null when a non-array type is registered and we are looking for an array');
	}
}

class phTestAbstractFindingCollection extends phAbstractFindingCollection
{
	public function recurseArray($item, $keys, $currentKey = 0)
	{
		return parent::recurseArray($item, $keys, $currentKey);
	}
	
	public function addData($name, $dataItem)
	{
		$this->_dataItems[$name] = $dataItem; 
	}
	
	public function register(phFormViewElement $element, phNameInfo $name, phCompositeDataCollection $collection)
	{
		
	}
	
	public function validate(phFormViewElement $element, phNameInfo $name, phCompositeDataCollection $collection)
	{
		
	}
	
	public function createIterator()
	{
		
	}
}
