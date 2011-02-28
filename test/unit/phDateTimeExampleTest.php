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
require_once realpath(dirname(__FILE__)) . '/../../examples/datetime/phDateTime.php';

/**
 * Tests the DateTime class from the examples
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage test
 */
class phDateTimeExampleTest extends phTestCase
{
	/**
	 * @var DateTime
	 */
	protected $dateTimeForm = null;
	
	public function setUp()
	{
		phViewLoader::setInstance(
			new phFileViewLoader(realpath(dirname(__FILE__) . '/../../examples/datetime'))
		);
		
		$this->dateTimeForm = new phDateTime('test', 'dateTimeView', 1999, 2001);
	}
	
	public function testSetAndGetCurrentDateTime()
	{
		$this->dateTimeForm->setCurrentDateTime('2000-01-01 01:01:01');
		
		$this->assertEquals(strtotime('2000-01-01 01:01:01'), $this->dateTimeForm->getCurrentDateTime(), 'time has been set properly');
	}
	
	public function testSelectsCorrectDate()
	{
		$this->dateTimeForm->setCurrentDateTime('2000-01-01 01:01:01');
		$xml = '<minacl>' . $this->dateTimeForm->__toString() . '</minacl>';
		/*
		 * replace the &nbsp; with nothing otherwise it'll cause an error
		 * (as we are not including the html entity definitions here)
		 */
		$xml = str_replace('&nbsp;', '', $xml);
		echo $xml;
		$dom = new SimpleXmlElement($xml);
		
		$year = $dom->xpath("//select[id='test_year']/option[selected='selected']");
		$this->assertEquals('2000', (string)$year[0], 'Selected year is 2000');
		$month = $dom->xpath("//select[id='test_month']/option[selected='selected']");
		$this->assertEquals('January', (string)$month[0], 'Selected month is January');
		$day = $dom->xpath("//select[id='test_day']/option[selected='selected']");
		$this->assertEquals('01', (string)$day[0], 'Selected day is 01');
		
		$hour = $dom->xpath("//select[id='test_hour']/option[selected='selected']");
		$this->assertEquals('01', (string)$hour[0], 'Selected hour is 01');
		$minutes = $dom->xpath("//select[id='test_minute']/option[selected='selected']");
		$this->assertEquals('01', (string)$minutes[0], 'Selected minutes is 01');
		$seconds = $dom->xpath("//select[id='test_second']/option[selected='selected']");
		$this->assertEquals('01', (string)$seconds[0], 'Selected seconds is 01');
	}
}
