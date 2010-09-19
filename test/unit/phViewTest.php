<?php
require_once 'PHPUnit/Framework.php';
$path = realpath(dirname(__FILE__)) . '/../../lib/form/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'phForm.php';
require_once 'phFormView.php';

class phViewTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$this->template = realpath(dirname(__FILE__)) . '/../resources/viewTestView.php';
		$this->form = new phForm('test', $this->template);
		$this->view = new phFormView($this->template, $this->form);
	}
	
    public function testElementFinding()
    {
        $this->assertTrue($this->view->username instanceof phInputElement, 'username exists and is a phInputElement');
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
    
    public function testValidElementId()
    {
    	$this->assertEquals($this->view->id('idIsGood'), 'test_idIsGood', 'element id set ok');
    }
    
	public function testValidElementId2()
    {
    	$this->assertEquals($this->view->id('underscores_are_ok'), 'test_underscores_are_ok', 'element id set ok');
    }
    
    public function testRender()
    {
    	$html = $this->view->render();
    	
    	$template = realpath(dirname(__FILE__)) . '/../resources/viewTestView.php';
		$view = new phFormViewTest($template, new phForm('test', $template));
		
		$this->assertEquals(new SimpleXMLElement($html), $view->getDom(), 'Rendered HTML is same as original');
    }
    
    public function testSetValue()
    {
    	$this->view->username->setValue("Test123 ABC");
    	$html = $this->view->render();
    	
    	$this->assertContains('"Test123 ABC"', $html, 'The username value was set and rendered correctly');
    }
    
    public function testElementGrabbing()
    {
    	$elements = $this->view->getElementsFromName('checkbox');
    	$this->assertEquals(sizeof($elements), 3, '3 elements returned from getElementsFromName(\'checkbox\')');
    }
    
	public function testSubFormElementGrabbing()
    {
    	$this->form->addForm(new phForm('validName', $this->template));
    	$elements = $this->view->getElementsFromName('validName');
    	$this->assertEquals(sizeof($elements), 1, '1 element returned from getElementsFromName(\'validName\')');
    	$this->assertTrue($elements[0] instanceof phForm, 'returned element is a phForm');
    }
    
    public function testGetByNameReturnsSameInstance()
    {
    	$username1 = $this->view->getElementsFromName('username');
    	$username2 = $this->view->getElementsFromName('username');
    	
    	$this->assertSame($username1, $username2, 'getElementsFromName returns a reference and isn\'t creating a new object each time');
    }
    
	public function testMagicGetReturnsSameInstance()
    {
    	$username1 = $this->username;
    	$username2 = $this->username;
    	
    	$this->assertSame($username1, $username2, '__get returns a reference and isn\'t creating a new object each time');
    }
}

class phFormViewTest extends phFormView
{
	public function getDom()
	{
		return parent::getDom();
	}
}
?>
