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

class phViewTest extends phTestCase
{
	protected function setUp()
	{
		parent::setup();
		
		$this->template = 'renderTestView';
		$this->form = new phForm('test', $this->template);
		$this->view = new phFormView($this->template, $this->form);
	}
	
    public function testElementFinding()
    {
        $this->assertTrue($this->view->getData('username') instanceof phFormDataItem, 'username exists and is a phFormDataItem');
    }
    
    /**
     * @expectedException phFormException
     */
    public function testElementNotfound()
    {
    	$this->view->getData('elementDoesNotExist');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testIdRegisteredButNotInView()
    {
    	$this->view->id('missing');
    	$this->view->getData('missing');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testNameRegisteredButNotInView()
    {
    	$this->view->name('missing');
    	$this->view->getData('missing');
    }
    
    
    /**
     * @expectedException phFormException
     */
    public function testNoFactory()
    {
    	$template = 'invalidElementView';
    	$view = new phFormView($template, new phForm('test', $template));
    	$view->getData('invalid');
    }
    
    /**
     * @expectedException phFormException
     */
    public function testRewrittenNameNotFound()
    {
    	$this->view->getRewrittenName('nonexistantname');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testRealIdNotFound()
    {
    	$this->view->getRealId('nonexistantid');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testElementNameButNoId()
    {
    	$template = 'invalidElementView';
    	$view = new phFormView($template, new phForm('test', $template));
    	$view->getData('noId');
    }
    
    /**
     * @expectedException phFormException
     */
    public function testInvalidElementId()
    {
    	$this->view->id('123nostartwithnumbers');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidElementId2()
    {
    	$this->view->id('no spaces');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidElementId3()
    {
    	$this->view->id('$pecialch£rsnotallowed');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidElementId4()
    {
    	$this->view->id('_noUnderscoreStart');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidElementId5()
    {
    	$this->view->id('cannotEndWith.');
    }
    
    public function testValidElementId()
    {
    	$this->assertEquals($this->view->id('idIsGood'), 'test_idIsGood', 'element id set ok');
    }
    
	public function testValidElementId2()
    {
    	$this->assertEquals($this->view->id('underscores_are_ok'), 'test_underscores_are_ok', 'element id set ok');
    }
    
    /**
     * Test that we can use dot notation to get access to ids on subforms
     */
    public function testIdForSubform()
    {
    	/*
    	 * add 2 subforms and make sure we can go 2 levels down
    	 */
    	$subform = new phForm('address', 'addressTestView');
    	$subform2 = new phForm('address2', 'addressTestView');
    	$subform->addForm($subform2);
    	$this->form->addForm($subform);
    	
    	$this->assertEquals('test_address_address', $this->view->id('address.address'), 'id for subform returned correctly');
    	$this->assertEquals('test_address_address2_address', $this->view->id('address.address2.address'), 'id for subform of subform returned correctly');
    }
    
    /**
     * @expectedException phFormException
     */
    public function testNonExistantSubformId()
    {
    	/*
    	 * there is no subform "nonexistant" - this should throw an error
    	 */
    	$this->view->id('nonexistant.address');
    }
    
    public function testValidNames()
    {
    	$this->assertEquals($this->view->name('goodName'), 'test[goodName]');
    	$this->assertEquals($this->view->name('nameWithNumbers123'), 'test[nameWithNumbers123]');
    	$this->assertEquals($this->view->name('arrayData[]'), 'test[arrayData][]');
    	$this->assertEquals($this->view->name('associativeArray[key]'), 'test[associativeArray][key]');
    	$this->assertEquals($this->view->name('numericKey[1]'), 'test[numericKey][1]');
    	$this->assertEquals($this->view->name('multiDimensional[1][1]'), 'test[multiDimensional][1][1]');
    }
    
    /**
     * @expectedException phFormException
     */
    public function testInvalidNameWithSpaces()
    {
    	$this->view->name('Bad Name');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName2()
    {
    	$this->view->name('BadName[');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName3()
    {
    	$this->view->name('BadN]a[me');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName4()
    {
    	$this->view->name('123BadName'); // can't start with numbers
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName5()
    {
    	$this->view->name('_BadName'); // can't start with _
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName6()
    {
    	$this->view->name('BadName[]moo'); // can't have anything after the array close
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName7()
    {
    	$this->view->name('BadName[[]'); // bad character in the array key
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName8()
    {
    	$this->view->name('BadName[@]'); // bad character in the array key
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName9()
    {
    	$this->view->name('BadName]'); // unopened array
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName10()
    {
    	$this->view->name('BadName['); // unclosed array
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName11()
    {
    	$this->view->name('badName');
    	$this->view->name('badName[]'); // badName is already registered
    }
    
    public function testRender()
    {
    	$html = $this->view->render();
    	
    	$template = 'renderTestView';
		$view = new phFormViewTest($template, new phForm('test', $template));
		$this->assertEquals($html, $view->render(), 'Rendered HTML is same as original');
    }
    
    public function testSetValue()
    {
    	$this->view->getData('username')->bind("Test123 ABC");
    	$html = $this->view->render();
    	
    	$this->assertContains('"Test123 ABC"', $html, 'The username value was set and rendered correctly');
    }
    
	public function testMagicGetReturnsSameInstance()
    {
    	$username1 = $this->view->getData('username');
    	$username2 = $this->view->getData('username');
    	
    	$this->assertSame($username1, $username2, '__get returns a reference and isn\'t creating a new object each time');
    }
    
    /**
     * @expectedException phFormException
     */
    public function testCrapHtml()
    {
    	$template = 'crapHtmlView';
    	$view = new phFormView($template, new phForm('test', $template));
    	$view->getData('rubbish'); // trigger the initialise
    }
    
    public function testErrorListing()
    {
    	/*
    	 * Had a bug with initialize and errorList together that resulted in
    	 * a call to a method on a non object. So I'm replicating here, that
    	 * for a form with a configure method on first pass no error is 
    	 * rendered and that no exceptions/php errors happen
    	 */
    	$form = new phTestErrorsConfigureForm('test', $this->template);
    	$content = (string)$form;
    	$this->assertEquals(0, preg_match_all('/<ul>/', $content, $matches), 'no error lists rendered');
    	
    	$username = $form->username;
    	$username->setValidator(new phRequiredValidator(array(phRequiredValidator::REQUIRED=>'Field is required')));
    	$username->bind('');
    	$username->validate();
    	
    	$content = (string)$form;
    	$this->assertEquals(1, preg_match_all('/<li>Field is required<\/li>/', $content, $matches), 'Error rendered properly');
    }
    
    public function testGetElement()
    {
    	$username = $this->view->getElement('username');
    	$this->assertTrue($username instanceof phInputElement, 'username is a phInputElement');
    	
    	$rewrittenId = $this->view->id('username');
    	$this->assertEquals((string)$username->getElement()->attributes()->id, $rewrittenId, 'getElement returned the correct element');
    }
    
    /**
     * @expectedException phFormException
     */
	public function testGetElementWithNonExistantElement()
    {
    	$username = $this->view->getElement('nonExistantId');
    }
    
    public function testArrays()
    {
    	$view = new phFormView('arrayTestView', new phForm('test', 'arrayTestView'));
    	$this->assertTrue($view->getData('ids') instanceof phArrayFormDataItem, 'The ids data is accessible and is an instance of phArrayFormDataItem');
    	$this->assertEquals(sizeof($view->getData('ids')), 5, 'There are 5 elements in ids');
    }
    
    /**
     * @expectedException phFormException
     */
    public function testElementsWithSameNameCannotAppearTwiceException()
    {
    	$view = new phFormView('elementsAppearingMultipleTimesTestView', new phForm('test', 'elementsAppearingMultipleTimesTestView'));
    	// need to do something that triggers initialize
    	$view->getAllData();
    }
    
    /**
     * Tests that elements that must be unique but have an array
     * name with an auto key - i.e. "ids[]" doesn't throw an 
     * exception
     */
    public function testValidMultipleElementsWithAutoArray()
    {
    	$view = new phFormView('validMultipleElementsTestView', new phForm('test', 'validMultipleElementsTestView'));
    	// need to do something that triggers initialize
    	$view->getAllData();
    }
    
    /**
     * Tests that you can set your own variable on the view and when rendered
     * that variable is accessible in the template
     */
    public function testSetCustomVariable()
    {
    	$view = new phFormView('customVariableView', new phForm('test', 'customVariableView'));
    	$view->test = "This is testing minacle's variable functionality";
    	$this->assertEquals("This is testing minacle's variable functionality", $view->test, 'View variable set properly');
    	$out = $view->render();
    	$this->assertEquals("This is testing minacle's variable functionality", $view->render(), 'Variable accessible in the view template');
    }
    
    /**
     * tests that we cannot start custom variables with '_'
     * @expectedException phFormException
     */
    public function testReservedVariable()
    {
    	$this->view->_notOk = true;
    }
    
    /**
     * Test that trying to access a non-existant property generates an error
     * @expectedException PHPUnit_Framework_Error
     */
    public function testNonExistantCustomVariable()
    {
    	$this->view->doesNotExist;
    }
    
    public function testHtmlEntities()
    {
    	$view = new phFormView('htmlEntityView', new phForm('test', 'htmlEntityView'));
    	$view->render();
    	$this->assertTrue(true, 'no errors on parsing some html entities');
    }
    
	/**
     * Test that using a template for the view in anything other than UTF-8 causes
     * an exception, this is a solution to the issue - 
     * https://github.com/mellowplace/PHP-HTML-Driven-Forms/issues/3
     * 
     * @expectedException phFormException
     */
    public function testCp1252Template()
    {
    	$form = new phForm('test', 'cp1252View');
    	$form->getView()->render();
    }
}

class phFormViewTest extends phFormView
{
	public function getDom()
	{
		return parent::getDom();
	}
}

/**
 * This is used in the testErrorListing method, see the
 * comment there for why it is here
 */
class phTestErrorsConfigureForm extends phForm
{
	public function configure()
	{
		$this->username;
	}
}
?>
