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

/**
 * this test case tests various rendering aspects of the phForm library
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage test
 */
class phRenderingTest extends phTestCase
{
	public function setUp()
	{
		parent::setUp();
		
		$this->form = new phForm('test', 'xhtmlView');
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
	
	/**
	 * <textarea></textarea> gets rewritten by simplexml to <textarea /> which is invalid
	 * HTML and causes the rest of the pages HTML to be the contents of the textarea tag.
	 * 
	 * The issue was first reported here -
	 * https://github.com/mellowplace/PHP-HTML-Driven-Forms/issues/2
	 */
	public function testEmptyTextArea()
	{
		$form = new phForm('test', 'emptyTextAreaView');
		$html = $form->__toString();
		$this->assertTrue(strpos($html, '<textarea name="test[notes]" id="test_notes"></textarea>')!==false, 'Empty text area tag was not written as a shorthand <textarea />');
	}
}