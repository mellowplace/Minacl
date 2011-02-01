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

require_once realpath(dirname(__FILE__)) . '/../resources/phTestForm.php';

/**
 * this test case tests subforms can be embedded and work properly
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage test
 */
class phSubFormTest extends phTestCase
{
	public function setUp()
	{
		parent::setUp();
		
		$this->form = new phTestForm('test', 'subFormTestView');
		$addressForm = new phTestForm('address', 'addressTestView');
		$this->form->addForm($addressForm);
	}
	
	public function testSubFormElementsAccessible()
	{
		$this->assertTrue($this->form->address->address instanceof phFormDataItem, 'Address on the subform is accessible');
		$this->assertTrue($this->form->address->postal_code instanceof phFormDataItem, 'Postal code on the subform is accessible');
	}
	
	public function testSubFormFillin()
	{
		$this->form->bind(array(
				'first_name'=>'Roberto',
				'address'=>array(
					'address'=>'123 A Street',
					'postal_code'=>'PC123'
				)
			)	
		);
		
		$html = $this->form->__toString();
		
		$xml = new SimpleXMLElement("<xhtml>{$html}</xhtml>");
		
		$elements = $xml->xpath('//input[@value=\'Roberto\']');
		
		$this->assertEquals(sizeof($elements), 1, 'found an input matching the addresses value');
    	$rewrittenName = $this->form->getView()->name('first_name');
    	$this->assertEquals((string)$elements[0]->attributes()->name, $rewrittenName, 'the name attribute matches the rewritten name for the first_name field');
    	$this->assertEquals($this->form->first_name->getValue(), 'Roberto', 'first_name field was set correctly');
    	
		$elements = $xml->xpath('//input[@value=\'123 A Street\']');
    	
    	$this->assertEquals(sizeof($elements), 1, 'found an input matching the addresses value');
    	$rewrittenName = $this->form->address->getView()->name('address');
    	$this->assertEquals((string)$elements[0]->attributes()->name, $rewrittenName, 'the name attribute matches the rewritten name for the address field');
    	$this->assertEquals($this->form->address->address->getValue(), '123 A Street', 'address field was set correctly');
    	
    	
    	$elements = $xml->xpath('//input[@value=\'PC123\']');
    	
    	$this->assertEquals(sizeof($elements), 1, 'found an input matching the postal code value');
    	$rewrittenName = $this->form->address->getView()->name('postal_code');
    	$this->assertEquals((string)$elements[0]->attributes()->name, $rewrittenName, 'the name attribute matches the rewritten name for postal code');
    	$this->assertEquals($this->form->address->postal_code->getValue(), 'PC123', 'postal code field was set correctly');
	}
	
	public function testSubFormValidation()
	{
		$this->form->address->address->setValidator(new phRequiredValidator(
			array(phRequiredValidator::REQUIRED=>'Required field')
		));
		$this->form->bindAndValidate(array(
			'first_name'=>'Rob',
			'address'=>array(
				'address'=>'A Street',
				'postal_code'=>'P11'
			)
		));
		$this->assertTrue($this->form->isValid(), 'Form is valid');
		
		$this->form->address->address->setValidator(new phRequiredValidator(
			array(phRequiredValidator::REQUIRED=>'Required field')
		));
		$this->form->bind(array());
		$this->assertFalse($this->form->isValid(), 'Form is not valid');
	}
}