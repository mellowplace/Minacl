<?php
require_once 'PHPUnit/Framework.php';
 
class phFormTest extends PHPUnit_Framework_TestCase
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
    
    private function addForm($name)
    {
    	$this->form = new phForm(realpath(dirname(__FILE__)) . '/../resources/viewTestView.php');
    	$this->addedForm = new phForm(realpath(dirname(__FILE__)). '/../resources/viewTestView.php');
        $this->form->addForm($name, $this->addedForm);
    }
}
?>