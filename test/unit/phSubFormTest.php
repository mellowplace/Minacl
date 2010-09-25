<?php
$path = realpath(dirname(__FILE__)) . '/../../lib/form/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'PHPUnit/Framework.php';
require_once 'phForm.php';

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
		$this->form = new phForm('test', $templateDir . 'subFormTestView.php');
		$addressForm = new phForm('address', $templateDir . 'addressTestView.php');
		$this->form->addForm($addressForm);
	}
	
	public function testSubFormElementsAccessible()
	{
		$this->assertTrue($this->form->address->address instanceof phInputElement, 'Address on the subform is accessible');
		$this->assertTrue($this->form->address->postalCode instanceof phInputElement, 'Postal code on the subform is accessible');
	}
}