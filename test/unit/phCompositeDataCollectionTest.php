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
 * Tests the phCompositeDataCollection class
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage test
 */
class phCompositeDataCollectionTest extends phTestCase
{
	public function setUp()
	{
		parent::setUp();
		
		$this->collection = new phCompositeDataCollection();
	}
	
	public function testBasic()
	{
		$info = new phNameInfo('address');
		$collection1 = new phTestDataCollection();
		$element1 = new phTestElement($collection1);
		
		$this->collection->register($element1, $info);
		
		$this->assertEquals(sizeof($collection1->_dataItems), 1, 'After registering one element collection1 has 1 data item');
		
		$collection2 = new phTestDataCollection();
		$element2 = new phTestElement2($collection2);
		$info = new phNameInfo('name');
		
		$this->collection->register($element2, $info);
		
		$this->assertEquals(sizeof($collection1->_dataItems), 1, 'After registering another element collection1 still has 1 data item....');
		$this->assertEquals(sizeof($collection2->_dataItems), 1, 'and collection2 now also has 1 element');
		
		$info = new phNameInfo('surname');
		$element3 = new phTestElement($collection1);
		
		$this->collection->register($element3, $info);
		
		$this->assertEquals(sizeof($collection1->_dataItems), 2, 'After registering another element collection1 now has 2 data items....');
		$this->assertEquals(sizeof($collection2->_dataItems), 1, 'and collection2 still has 1 element');
		
		$dataItem = $this->collection->find('surname');
		$this->assertSame($collection1->_dataItems['surname'], $dataItem, 'find(\'surname\') has returned the correct dataItem from collection1');
		
		$items = $this->collection->createIterator();
		$testData = array();
		foreach($items as $k=>$v)
		{
			$testData[$k] = $v;
		}
		
		$this->assertTrue(array_key_exists('address', $testData), 'address data item was returned in the iterator');
		$this->assertTrue(array_key_exists('name', $testData), 'name data item was returned in the iterator');
		$this->assertTrue(array_key_exists('surname', $testData), 'surname data item was returned in the iterator');
	}
	
	/**
	 * @expectedException phFormException
	 */
	public function testValidateFail()
	{
		$info = new phNameInfo('address');
		$collection1 = new phTestDataCollection(true);
		$element1 = new phTestElement($collection1);
		
		$this->collection->register($element1, $info);
	}
	
	/**
	 * Tests that, when an array is split across 2 collections that array is combined
	 * and returned properly
	 */
	public function testCombineArrays()
	{
		$info = new phNameInfo('ids[]');
		$collection1 = new phSimpleDataCollection();
		$element1 = new phTestElement($collection1);
		
		$this->collection->register($element1, $info);
		
		$collection2 = new phSimpleDataCollection();
		$element2 = new phTestElement2($collection2);
		$info = new phNameInfo('ids[]');
		
		$this->collection->register($element2, $info);
		
		$ids = $this->collection->find('ids');
		$this->assertEquals(2, sizeof($ids), 'ids has 2 elements');
		$this->assertSame($ids[0], $element1->_boundDataItem, 'ids[0] is the item that was bound to element1');
		$this->assertSame($ids[1], $element2->_boundDataItem, 'ids[1] is the item that was bound to element2');
	}
}

class phTestElement implements phFormViewElement
{
	protected $_dataCollection = null;
	public $_boundDataItem = null;
	
	public function __construct(phDataCollection $collection)
	{
		$this->_dataCollection = $collection;
	}
	
	public function createDataCollection()
	{
		return $this->_dataCollection;
	}
	
	public function bindDataItem(phFormDataItem $item)
	{
		$this->_boundDataItem = $item;
	}
}

class phTestElement2 extends phTestElement
{
	
}

class phTestDataCollection implements phDataCollection
{
	public $_dataItems = array();
	public $_failValidate = false;
	
	public function __construct($failValidate = false)
	{
		$this->_failValidate = $failValidate;
	}
	
	/**
	 * Registers an element with this collection
	 * 
	 * @param phFormViewElement $element
	 * @param phNameInfo $name
	 */
	public function register(phFormViewElement $element, phNameInfo $name, phCompositeDataCollection $collection)
	{
		$item = new phFormDataItem($name->getName());
		$this->_dataItems[$name->getName()] = $item;
		$element->bindDataItem($item);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phDataCollection::validate()
	 */
	public function validate(phFormViewElement $element, phNameInfo $name, phCompositeDataCollection $collection)
	{
		if($this->_failValidate)
		{
			throw new phFormException("Validate has failed!");
		}
	}
	
	/**
	 * @return Iterator an iterator for going over the phData objects in this collection
	 */
	public function createIterator()
	{
		return new ArrayIterator($this->_dataItems);
	}
	
	/**
	 * Gets a phData item referred to by $name
	 * 
	 * @param string $name
	 * @return phData
	 */
	public function find($name)
	{
		if(!array_key_exists($name, $this->_dataItems))
		{
			return null;
		}
		
		return $this->_dataItems[$name];
	}
}