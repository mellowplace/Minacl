<?php
$path = realpath(dirname(__FILE__)) . '/../../lib/form/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'PHPUnit/Framework.php';
require_once 'phForm.php';
require_once realpath(dirname(__FILE__)) . '/../resources/phTestForm.php';
require_once 'validator/phRequiredValidator.php';

/**
 * this test case tests subforms can be embedded and work properly
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage test
 */
class phSubFormTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$templateDir = realpath(dirname(__FILE__)) . '/../resources/';
		$this->form = new phTestForm('test', $templateDir . 'subFormTestView.php');
		$addressForm = new phTestForm('address', $templateDir . 'addressTestView.php');
		$this->form->addForm($addressForm);
	}
	
	public function testSubFormElementsAccessible()
	{
		$this->assertTrue($this->form->address->address instanceof phFormDataItem, 'Address on the subform is accessible');
		$this->assertTrue($this->form->address->postal_code instanceof phFormDataItem, 'Postal code on the subform is accessible');
	}
	
	public function testSubFormFillin()
	{
		$this->form->address->address->bind('123 A Street');
		$this->form->address->postal_code->bind('PC123');
		
		$html = $this->form->__toString();
		$xml = new SimpleXMLElement("<xhtml>{$html}</xhtml>");
		
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
		$this->form->address->address->setValidator(new phRequiredValidator('Required field'));
		$this->form->bindAndValidate(array(
			'first_name'=>'Rob',
			'address'=>array(
				'address'=>'A Street',
				'postal_code'=>'P11'
			)
		));
		$this->assertTrue($this->form->isValid(), 'Form is valid');
		
		$this->form->address->address->setValidator(new phRequiredValidator('Required field'));
		$this->form->bind(array());
		$this->assertFalse($this->form->isValid(), 'Form is not valid');
	}
}