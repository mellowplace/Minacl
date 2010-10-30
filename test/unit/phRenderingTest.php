<?php
require_once 'PHPUnit/Framework.php';
require_once realpath(dirname(__FILE__)) . '/../../lib/form/phLoader.php';
phLoader::registerAutoloader();

/**
 * this test case tests various rendering aspects of the phForm library
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage test
 */
class phRenderingTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->form = new phForm('test', realpath(dirname(__FILE__)) . '/../resources/xhtmlView.php');
		$this->xhtmlDecl = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" 
        \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
	}
	
	public function testValidXhtml()
	{
		$xhtml = $this->xhtmlDecl . $this->form->__toString();
		
		$doc = new DOMDocument();
		$doc->loadXML($xhtml);
		$this->assertTrue($doc->validate(), 'The rendered form is valid xhtml');
	}
	
	public function testSpecialCharsFillinValidXhtml()
	{
		$this->form->bind(array(
			'username'=>'&<>&amp;',
			'password'=>'&%$""<>'
		));
		
		$xhtml = $this->xhtmlDecl . $this->form->__toString();
		$doc = new DOMDocument();
		$doc->loadXML($xhtml);
		$this->assertTrue($doc->validate(), 'The rendered form is valid xhtml');
	}
}