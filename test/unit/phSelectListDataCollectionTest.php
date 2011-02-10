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
require_once realpath(dirname(__FILE__)) . '/../resources/phSimpleTestElement.php';

/**
 * Tests the phSelectListDataCollection class
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage test
 */
class phSelectListDataCollectionTest extends phAbstractSelectListTest
{
	/**
	 * @var phSelectListDataCollection
	 */
	protected $collection = null;
	/**
	 * @var phTestSelectListCompositeDataCollection
	 */
	protected $composite = null;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->collection = new phSelectListDataCollection();
		$this->composite = new phTestSelectListCompositeDataCollection($this->collection);
	}
	
	/**
	 * Should error because the same name with auto keys for multi selects cannot be registered twice
	 *
	 * @expectedException phFormException
	 */
	public function testSameMultiAutoKeyNotOk()
	{
		$options = array(
			'A' => 'Option A',
			'B' => 'Option B'
		);
		$info = new phNameInfo('options[]');
		$this->composite->register($this->createSelectListElement('options[]', $options, true), $info);
		$this->collection->validate($this->createSelectListElement('options[]', $options, true), $info, $this->composite);
	}
	
	
	public function testSameSingleKeyOk()
	{
		$options = array(
			'A' => 'Option A',
			'B' => 'Option B'
		);
		$info = new phNameInfo('options[]');
		$this->composite->register($this->createSelectListElement('options[]', $options, false), $info);
		$this->collection->validate($this->createSelectListElement('options[]', $options, false), $info, $this->composite);
		$this->assertTrue(true, '2 selects with the same auto key names are ok if they are not multi selects');
	}
	
	/**
	 * Test that if you register a single select with auto key (e.g. options[]) that you cannot
	 * register a multi select with the same name
	 * 
	 * @expectedException phFormException
	 */
	public function testMixSingleAndMultiWithSameAutoKeyNotOk()
	{
		$options = array(
			'A' => 'Option A',
			'B' => 'Option B'
		);
		$info = new phNameInfo('options[]');
		$this->composite->register($this->createSelectListElement('options[]', $options, false), $info);
		$this->collection->validate($this->createSelectListElement('options[]', $options, true), $info, $this->composite);
	}
	
	/**
	 * Same as above but register multi select first
	 * 
	 * @expectedException phFormException
	 */
	public function testMixSingleAndMultiWithSameAutoKeyNotOkReverse()
	{
		$options = array(
			'A' => 'Option A',
			'B' => 'Option B'
		);
		$info = new phNameInfo('options[]');
		$this->composite->register($this->createSelectListElement('options[]', $options, true), $info);
		$this->collection->validate($this->createSelectListElement('options[]', $options, false), $info, $this->composite);
	}
	
	/**
	 * Test that the element must have a unique name
	 * 
	 * @expectedException phFormException
	 */
	public function testUniqueNameRequired()
	{
		$options = array(
			'A' => 'Option A',
			'B' => 'Option B'
		);
		$info = new phNameInfo('options');
		$this->composite->register($this->createSelectListElement('options', $options, false), $info);
		$this->collection->validate($this->createSelectListElement('options', $options, false), $info, $this->composite);
	}
	
	/**
	 * Test that multi selects must use an auto key in their name (with
	 * php if you had a multi select with just the name "option", for example,
	 * when you submitted the form you'd get something like option=A&option=B&option=C
	 * This would not be readable you'd only get one value - C, so we force
	 * an auto key name like option[]
	 * 
	 * @expectedException phFormException
	 */
	public function testMultiSelectNeedsautoKey()
	{
		$options = array(
			'A' => 'Option A',
			'B' => 'Option B'
		);
		$info = new phNameInfo('options');
		$this->collection->validate($this->createSelectListElement('options', $options, true), $info, $this->composite);
	}
	
	/**
	 * Test that if an element is registered at options[] that we cannot put a multi-select there
	 * 
	 * @expectedException phFormException
	 */
	public function testOtherElementAtAutoArray()
	{
		$options = array(
			'A' => 'Option A',
			'B' => 'Option B'
		);
		$info = new phNameInfo('options[]');
		$this->composite->register(new phSimpleTestElement(), $info);
		$this->collection->validate($this->createSelectListElement('options[]', $options, false), $info, $this->composite);
	}
	
	/**
	 * Same as above but register the multi select first
	 * 
	 * @expectedException phFormException
	 */
	public function testOtherElementAtAutoArrayReverse()
	{
		$options = array(
			'A' => 'Option A',
			'B' => 'Option B'
		);
		$info = new phNameInfo('options[]');
		$this->composite->register($this->createSelectListElement('options[]', $options, false), $info);
		$this->collection->validate(new phSimpleTestElement(), $info, $this->composite);
	}
}

class phTestSelectListCompositeDataCollection extends phCompositeDataCollection
{
	public function __construct(phSelectListDataCollection $collection)
	{
		$this->_collections['phSelectListElement'] = $collection;
	}
}