<?php
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
    		'checkbox'=>'3'
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
    
    private function addForm($name)
    {
    	$this->form = new phForm('test', 'viewTestView');
    	$this->addedForm = new phForm($name, 'viewTestView');
        $this->form->addForm($this->addedForm);
    }
}
?>
