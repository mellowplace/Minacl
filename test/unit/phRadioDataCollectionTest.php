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
require_once realpath(dirname(__FILE__)) . '/../resources/phTestFormView.php';

/**
 * Tests for the phRadioDataCollection class
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage test
 */
class phRadioDataCollectionTest extends phTestCase
{
	public function setUp()
	{
		parent::setUp();
		
		$this->collection = new phRadioDataCollection();
		$this->composite = new phTestRadioCompositeDataCollection($this->collection);
	}
	
	/**
	 * Make sure that you cannot register names with auto keys
	 * e.g. ids[] as this would cause problems because any browser
	 * will see it as the same name and therefore part of the same
	 * group but PHP would see it as separate data
	 * 
	 * @expectedException phFormException
	 */
	public function testValidateAutoKeys()
	{
		$element = $this->createRadioButton('ids[]');
		$name = new phNameInfo('ids[]');
		
		$this->collection->validate($element, $name, $this->composite);
	}
	
	/**
	 * Same as above test but testing another level down
	 * 
	 * @expectedException phFormException
	 */
	public function testValidateAutoKeysNextLevel()
	{
		$element = $this->createRadioButton('test[ids][]');
		$name = new phNameInfo('test[ids][]');
		
		$this->collection->validate($element, $name, $this->composite);
	}
	
	/**
	 * Test when using the same name you cannot add a non unique value
	 * 
	 * @expectedException phFormException
	 */
	public function testValuesWithNameUnique()
	{
		$element = $this->createRadioButton('type', 'test');
		$name = new phNameInfo('type');
		
		$this->composite->register($element, $name);
		
		$this->collection->validate($element, $name, $this->composite); // should error as value of test is not unique
	}
	
	/**
	 * Same as above but with arrays
	 * 
	 * @expectedException phFormException
	 */
	public function testValuesWithArrayNameUnique()
	{
		$element = $this->createRadioButton('test[type]', 'test');
		$name = new phNameInfo('type');
		
		$this->composite->register($element, $name);
		
		$this->collection->validate($element, $name, $this->composite); // should error as value of test[type] is not unique
	}
	
	/**
	 * Register a radio button and check another with the same name validates ok
	 */
	public function testValidateOk()
	{
		$element = $this->createRadioButton('type', '1');
		$name = new phNameInfo('type');
		
		$this->composite->register($element, $name);
		
		$element = $this->createRadioButton('type', '2');
		$this->collection->validate($element, $name, $this->composite);
		
		$this->assertTrue(true, 'validate passes when items of the same name are radio buttons');
	}
	
	/**
	 * Same as above but with arrays
	 */
	public function testValidateOkArray()
	{
		$element = $this->createRadioButton('test[type]', '1');
		$name = new phNameInfo('test[type]');
		
		$this->composite->register($element, $name);
		
		$element = $this->createRadioButton('test[type]', '2');
		$this->collection->validate($element, $name, $this->composite);
		
		$this->assertTrue(true, 'validate passes when items of the same array type name are radio buttons');
	}
	
	public function testSameDataItemBound()
	{
		$element = $this->createRadioButton('type', '1');
		$name = new phNameInfo('type');
		
		$this->composite->register($element, $name);
		$item = $this->collection->find('type');
		$this->assertTrue($item instanceof phFormDataItem, 'found data is a phFormDataItem');
		
		$this->assertSame($element->boundDataItem, $item, 'found data item "type" is the same item that was bound to element');
		
		/*
		 * bind another element with the same name and make sure it's still
		 * the same data item bound to it
		 */
		$element2 = $this->createRadioButton('type', '2');
		$this->composite->register($element2, $name);
		
		$this->assertSame($element2->boundDataItem, $item, 'found data item "type" is the same item that was bound to element2');
	}
	
	/**
	 * same as above test but with arrays
	 */
	public function testSameDataItemBoundArray()
	{
		$element = $this->createRadioButton('test[type]', '1');
		$name = new phNameInfo('test[type]');
		
		$this->composite->register($element, $name);
		$item = $this->collection->find('test[type]');
		$this->assertTrue($item instanceof phFormDataItem, 'found data is a phFormDataItem');
		
		$this->assertSame($element->boundDataItem, $item, 'found data item "type" is the same item that was bound to element');
		
		/*
		 * bind another element with the same name and make sure it's still
		 * the same data item bound to it
		 */
		$element2 = $this->createRadioButton('test[type]', '2');
		$this->composite->register($element2, $name);
		
		$this->assertSame($element2->boundDataItem, $item, 'found data item "type" is the same item that was bound to element2');
	}
	
	private function createRadioButton($name, $value = "1")
	{
		return new phTestRadioButtonElement(new SimpleXMLElement("<input type=\"radio\" name=\"{$name}\" value=\"{$value}\" />"), new phTestFormView());
	}
}

class phTestRadioCompositeDataCollection extends phCompositeDataCollection
{
	public function __construct(phRadioDataCollection $collection)
	{
		$this->_collections['phTestRadioButtonElement'] = $collection;
	}
}

class phTestRadioButtonElement extends phRadioButtonElement
{
	public $boundDataItem = null;
	
	public function bindDataItem(phFormDataItem $item)
	{
		parent::bindDataItem($item);
		
		$this->boundDataItem = $item;
	}
}