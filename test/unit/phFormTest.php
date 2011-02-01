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
require_once dirname(__FILE__) . '/../../lib/form/phLoader.php';
phLoader::registerAutoloader();

require_once realpath(dirname(__FILE__)) . '/../resources/phTestForm.php';
 
class phFormTest extends phTestCase
{	
	/**
     * @expectedException phFormException
     */
    public function testInvalidValidFormName()
    {
    	$this->addForm('no spaces allowed');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidValidFormName2()
    {
    	$this->addForm('special%$chars');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidValidFormName3()
    {
    	$this->addForm('123cannotstartwithnumber');
    }
    
    public function testValidFormName()
    {
    	$this->addForm('validName');
    	$this->assertSame($this->form->validName, $this->addedForm, 'Form has been added and is the original object');
    }
    
    /**
     * @expectedException phFormException
     */
    public function testFormNotFound()
    {
    	$this->addForm('validName');
    	$this->form->getForm('nonExistant');
    }
    
    /**
     * @expectedException phFormException
     */
    public function testAddSameForm()
    {
    	$this->addForm('validName');
    	$sameForm = new phForm('validName', 'viewTestView');
    	$this->form->addForm($sameForm); // should error as form with this name already added
    }
    
    public function testBind()
    {
    	$form = new phForm('test', 'simpleTestView');
    	$form->bindAndValidate(array(
    		'username'=>'test',
    		'password'=>'pass'
    	));
    	
    	$this->assertTrue($form->isValid(), 'form is correctly valid');
    }
    
    /**
     * tests that the phForm object fills in data
     */
    public function testFillIn()
    {
    	$form = new phTestForm('test', 'fillInTestView');
    	$form->bind(array(
    		'username'=>'the username',
    		'password'=>'the password',
    		'checkbox'=>array(2=>'3')
    	));
    	
    	$xml = new SimpleXMLElement('<xhtml>' . $form->__toString() . '</xhtml>');
    	$elements = $xml->xpath('//input[@value=\'the username\']');
    	
    	$this->assertEquals(sizeof($elements), 1, 'found an input matching the usernames value');
    	$rewrittenName = $form->getView()->name('username');
    	$this->assertEquals((string)$elements[0]->attributes()->name, $rewrittenName, 'the username name attribute matches the rewritten name for username');
    	$this->assertEquals($form->username->getValue(), 'the username', 'username field was set correctly');
    	
    	$elements = $xml->xpath('//input[@value=\'the password\']');
    	
    	$this->assertEquals(sizeof($elements), 1, 'found an input matching the password value');
    	$rewrittenName = $form->getView()->name('password');
    	$this->assertEquals((string)$elements[0]->attributes()->name, $rewrittenName, 'the username name attribute matches the rewritten name for password');
    	$this->assertEquals($form->password->getValue(), 'the password', 'password field was set correctly');
    	
    	$rewrittenId = $form->getView()->id('checkbox3');
    	$elements = $xml->xpath("//input[@id='{$rewrittenId}']");
    	
    	$this->assertEquals(sizeof($elements), 1, 'found the checkbox3');
    	$this->assertEquals((string)$elements[0]->attributes()->checked, 'checked', 'the checkbox is marked correctly as checked');
    	
    	$rewrittenId = $form->getView()->id('checkbox1');
    	$elements = $xml->xpath("//input[@id='{$rewrittenId}']");
    	
    	$this->assertEquals(sizeof($elements), 1, 'found the checkbox1');
    	$this->assertFalse(isset($elements[0]->attributes()->checked), 'the checkbox1 is not marked as checked');
    }
    
    /**
     * Test the fillin works with array type data
     */
    public function testArrayFillin()
    {
    	$form = new phTestForm('test', 'arrayFillInView');
    	$form->bind(array(
    		'ids' => array(0=>'1', 2=>'3'),
    		'test' => array('name' => 'Rob', 'address' => "A street\nA town")
    	));
    	
    	$xml = new SimpleXMLElement('<xhtml>' . $form->__toString() . '</xhtml>');
    	
    	$rewrittenName = $form->getView()->name('test[name]');
    	$elements = $xml->xpath('//input[@name=\''.$rewrittenName.'\']');
    	$this->assertEquals(sizeof($elements), 1, 'found an input matching the test[name]');
    	$this->assertEquals('Rob', (string)$elements[0]->attributes()->value, 'the value attribute was set to Rob');
    	
    	$rewrittenName = $form->getView()->name('test[address]');
    	$elements = $xml->xpath('//textarea[@name=\''.$rewrittenName.'\']');
    	$this->assertEquals(sizeof($elements), 1, 'found an input matching the test[address]');
    	$this->assertEquals("A street\nA town", (string)$elements[0], 'the value attribute was set to "A street\nA town"');
    	
    	$rewrittenId = $form->getView()->id('ids_1');
    	$elements = $xml->xpath('//input[@id=\''.$rewrittenId.'\']');
    	$this->assertEquals(sizeof($elements), 1, 'found an input matching the ids[1]');
    	$this->assertEquals('checked', (string)$elements[0]->attributes()->checked, 'the ids[1] checkbox is checked');
    	
    	$rewrittenId = $form->getView()->id('ids_2');
    	$elements = $xml->xpath('//input[@id=\''.$rewrittenId.'\']');
    	$this->assertEquals(sizeof($elements), 1, 'found an input matching the ids[2]');
    	$this->assertFalse(isset($elements[0]->attributes()->checked), 'the ids[2] checkbox is not checked');
    	
    	$rewrittenId = $form->getView()->id('ids_3');
    	$elements = $xml->xpath('//input[@id=\''.$rewrittenId.'\']');
    	$this->assertEquals(sizeof($elements), 1, 'found an input matching the ids[3]');
    	$this->assertEquals('checked', (string)$elements[0]->attributes()->checked, 'the ids[3] checkbox is checked');
    }
    
    public function testElementFinder()
    {
    	$form = new phForm('test', 'simpleTestView');
    	$this->assertTrue($form->element() instanceof phElementFinder, '$form->element() returns a phElementFinder object');
    	$this->assertTrue($form->element()->username instanceof phInputElement, '$form->element()->username returns a phInputElement object');
    }
    
    /**
     * tests that textareas are dealt with properly
     */
    public function testTextArea()
    {
    	$form = new phTestForm('test', 'textAreaTestView');
    	/*
    	 * check the default value of the text area can be set and in turn is set into
    	 * the textareas simple xml element
    	 */
    	$form->textarea->bind('test value');
    	
    	/*
    	 * check the textarea's element value is set
    	 */
    	$this->assertEquals((string)$form->element()->textarea->getElement(), 'test value', 'the textareas value was set properly');
    }
    
    private function addForm($name)
    {
    	$this->form = new phForm('test', 'viewTestView');
    	$this->addedForm = new phForm($name, 'viewTestView');
        $this->form->addForm($this->addedForm);
    }
}
?>
