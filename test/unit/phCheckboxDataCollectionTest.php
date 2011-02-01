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

/**
 * Tests the phCheckboxDataCollection class
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage test
 */
class phCheckboxDataCollectionTest extends phTestCase
{
	/**
	 * @var phCheckboxDataCollection
	 */
	protected $collection = null;
	/**
	 * @var phTestCheckboxCompositeDataCollection
	 */
	protected $composite = null;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->collection = new phTestCheckboxDataCollection();
		$this->composite = new phTestCheckboxCompositeDataCollection($this->collection);
	}

	public function testSameNameUniqueOk()
	{
		$info = new phNameInfo('ids[]');
		$this->composite->register($this->createCheckboxElement('ids[]', '1'), $info);
		// check names registered ok
		$this->assertEquals('ids', $this->composite->find('ids')->getName(), 'array name set properly');
		$this->collection->validate($this->createCheckboxElement('ids[]', '2'), $info, $this->composite);
		$this->assertTrue(true, 'no problems with registering the same name but unique values');
	}

	/**
	 * Should error because it's the same name ("test") but in PHP will not generate an
	 * array and therefore pointless, even if it's valid HTML i.e. values are unique
	 *
	 * @expectedException phFormException
	 */
	public function testSameNameAndNotArray()
	{
		$info = new phNameInfo('test');
		$this->composite->register($this->createCheckboxElement('test', '1'), $info);
		$this->collection->validate($this->createCheckboxElement('test', '2'), $info, $this->composite);
	}
	
	/**
	 * Should error because it's the same name (ids[]) but the values are not unique
	 *
	 * @expectedException phFormException
	 */
	public function testNameOkButNotUnique()
	{
		$info = new phNameInfo('ids[]');
		$this->composite->register($this->createCheckboxElement('ids[]', '1'), $info);
		$this->collection->validate($this->createCheckboxElement('ids[]', '1'), $info, $this->composite);
	}
	
	/**
	 * Should error because there's already another type of data registered at ids[] that
	 * is not compatible with checkboxes
	 *
	 * @expectedException phFormException
	 */
	public function testMixingAutoKeyArraysIsNotOk()
	{
		$info = new phNameInfo('ids[]');
		$this->composite->register($this->createTextElement('ids[]', '1'), $info);
		$this->collection->validate($this->createCheckboxElement('ids[]', '2'), $info, $this->composite);
	}
	
	/**
	 * Same as above but register checkbox first then try normal type
	 *
	 * @expectedException phFormException
	 */
	public function testMixingAutoKeyArraysIsNotOkReverse()
	{
		$info = new phNameInfo('ids[]');
		$this->composite->register($this->createCheckboxElement('ids[]', '2'), $info);
		$this->collection->validate($this->createTextElement('ids[]', '1'), $info, $this->composite);
	}
	
	public function testNonAutoKeyArraysAreOk()
	{
		$info = new phNameInfo('ids[0]');
		$this->composite->register($this->createCheckboxElement('ids[0]', '2'), $info);
		$info = new phNameInfo('ids[1]');
		$this->collection->validate($this->createTextElement('ids[1]', '1'), $info, $this->composite);
		$this->assertTrue(true, 'normal key\'d arrays are ok');
	}
	
	/**
	 * Test the class isn't concerned with other element types
	 */
	public function testOtherElementsAreIgnored()
	{
		$info = new phNameInfo('name');
		$this->composite->register($this->createTextElement('name', 'rob'), $info);
		$this->collection->validate($this->createTextElement('name', 'rob'), $info, $this->composite);
		$this->assertTrue(true, 'checkbox collection ignores other data types');
	}
	
	public function testCreateArrayDataItem()
	{
		$info = new phArrayKeyInfo('', true, phArrayKeyInfo::NUMERIC);
		$type = $this->collection->createArrayDataItem($info);
		$this->assertTrue($type instanceof phCheckboxArrayDataItem, 'when an auto key is specified the data item is a phCheckboxArrayDataItem instance');
		
		$info = new phArrayKeyInfo('test', false, phArrayKeyInfo::STRING);
		$type = $this->collection->createArrayDataItem($info);
		$this->assertTrue($type instanceof phArrayFormDataItem, 'when a string key is specified the data item is a phArrayFormDataItem instance');
		
		$info = new phArrayKeyInfo(0, false, phArrayKeyInfo::NUMERIC);
		$type = $this->collection->createArrayDataItem($info);
		$this->assertTrue($type instanceof phArrayFormDataItem, 'when a numeric key is specified the data item is a phArrayFormDataItem instance');
		
		// test when name specified it uses that
		$info = new phArrayKeyInfo('', true, phArrayKeyInfo::NUMERIC);
		$type = $this->collection->createArrayDataItem($info, 'ids');
		$this->assertEquals('ids', $type->getName(), 'name override set properly');
	}
	
	public function testRegisterArrayKeys()
	{
		$info = new phNameInfo('ids[]');
		$ids1 = $this->createCheckboxElement('ids', '1');
		$this->composite->register($ids1, $info);
		$ids2 = $this->createCheckboxElement('ids', '2');
		$this->composite->register($ids2, $info);
		
		$idsData = $this->collection->find('ids');
		
		$this->assertTrue($idsData instanceof phCheckboxArrayDataItem, 'ids is a checkbox data array');
		$this->assertSame($idsData, $ids1->boundData, 'data item bound to ids1 is the ids data array');
		$this->assertSame($idsData, $ids2->boundData, 'data item bound to ids2 is the ids data array');
		
		$info = new phNameInfo('test[ids][]');
		$ids1 = $this->createCheckboxElement('ids', '1');
		$this->composite->register($ids1, $info);
		$ids2 = $this->createCheckboxElement('ids', '2');
		$this->composite->register($ids2, $info);
		
		$testData = $this->collection->find('test');
		$this->assertTrue($testData instanceof phArrayFormDataItem, 'test is a normal data array');
		$this->assertTrue($testData['ids'] instanceof phCheckboxArrayDataItem, 'test[ids] is a checkbox data array');
		$this->assertSame($testData['ids'], $ids1->boundData, 'data item bound to ids1 is the ids data array');
		$this->assertSame($testData['ids'], $ids2->boundData, 'data item bound to ids2 is the ids data array');
	}
	
	/**
	 * Test if binding autokeys and form fillin works
	 */
	public function testSpecialAutoKeyBinding()
	{
		$info = new phNameInfo('ids[]');
		$ids1 = $this->createCheckboxElement('ids', '1');
		$this->composite->register($ids1, $info);
		$ids2 = $this->createCheckboxElement('ids', '2');
		$this->composite->register($ids2, $info);
		
		$this->assertFalse($ids1->isChecked(), 'ids1 is not checked');
		$this->assertFalse($ids2->isChecked(), 'ids2 is not checked');
		
		$idsData = $this->collection->find('ids');
		$idsData->bind(array('1'));
		
		$this->assertTrue($ids1->isChecked(), 'ids1 *is* checked');
		$this->assertFalse($ids2->isChecked(), 'ids2 is still not checked');
	}
	
	private function createCheckboxElement($name, $value)
	{
		$xmlElement = new SimpleXMLElement("<input type=\"checkbox\" name=\"{$name}\" value=\"{$value}\" />");
		$phElement = new phTestCheckboxElement($xmlElement, new phTestFormView());
		return $phElement;
	}
	
	private function createTextElement($name, $value)
	{
		$xmlElement = new SimpleXMLElement("<input type=\"text\" name=\"{$name}\" value=\"{$value}\" />");
		$phElement = new phInputElement($xmlElement, new phTestFormView());
		return $phElement;
	}
}

class phTestCheckboxElement extends phCheckboxElement
{
	public $boundData = null;
	
	public function bindDataItem(phFormDataItem $item)
	{
		$this->boundData = $item;
		parent::bindDataItem($item);
	}
}

class phTestFormView extends phFormView
{
	public function __construct()
	{

	}
}

class phTestCheckboxDataCollection extends phCheckboxDataCollection
{
	public function createArrayDataItem(phArrayKeyInfo $info, $name = null)
	{
		return parent::createArrayDataItem($info, $name);
	}
}

class phTestCheckboxCompositeDataCollection extends phCompositeDataCollection
{
	public function __construct(phCheckboxDataCollection $collection)
	{
		$this->_collections['phTestCheckboxElement'] = $collection;
	}
}