<?php
require_once 'PHPUnit/Framework.php';
 
class phViewTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$template = realpath(dirname(__FILE__)) . '../resources/viewTestView.php';
		$this->view = new phFormView($template, new phForm($template));
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
    	$this->assertEquals($this->view->id('idIsGood'), 'idIsGood', 'element id set ok');
    }
    
	public function testValidElementId2()
    {
    	$this->assertEquals($this->view->id('underscores_are_ok'), 'underscores_are_ok', 'element id set ok');
    }
}
?>