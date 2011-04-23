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
 require_once(dirname(__FILE__) . '/../../lib/util/phReleaseNotesReflector.php');
 /**
  * Tests the phReleaseNotesReflector which is used in our packaging process
  *
  * @author Rob Graham <htmlforms@mellowplace.com>
  * @package phform
  */
class phReleaseNotesReflectorTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var phReleaseNotesReflector
	 */
	protected $reflector = null;
	
	public function setUp()
	{
		$this->reflector = new phReleaseNotesReflector(dirname(__FILE__) . '/../resources/changelog.md');
	}
	
	public function testMain()
	{
		$this->assertEquals("* The first changes made
* Created the library", $this->reflector->getChangesForRelease('0.1.0alpha'), '0.1.0alpha changes ok');
		$this->assertEquals("* The first release candidate", $this->reflector->getChangesForRelease('1.0.0RC1'), '1.0.0RC1 changes ok');
		$this->assertEquals("* Did some more stuff", $this->reflector->getChangesForRelease('0.2.0alpha'), '0.2.0alpha changes ok');
		$this->assertEquals("* Went beta with the library", $this->reflector->getChangesForRelease('0.3.0beta'), '0.3.0beta changes ok');
		$this->assertEquals("* The first actual release
* Made it stable", $this->reflector->getChangesForRelease('1.0.1'), '1.0.1 changes ok');
	}
	
	/**
	 * @expectedException Exception
	 */
	public function testNonExistantRelease()
	{
		// a release that has no changelog entry should error
		$this->reflector->getChangesForRelease('10.1.3');
	}
	
	/**
	 * @expectedException Exception
	 */
	public function testNonExistantRelease2()
	{
		// 0.1.0alpha does exist but this should not be found!
		$this->reflector->getChangesForRelease('0.1.0');
	}
	
	public function testGetPearVersionAndStability()
	{
		$info = $this->reflector->getPearVersionAndStability('0.1.1alpha');
		$this->assertEquals('0.1.1', $info['version'], 'version ok');
		$this->assertEquals('alpha', $info['stability'], 'stability ok');
		
		$info = $this->reflector->getPearVersionAndStability('1.2.3');
		$this->assertEquals('1.2.3', $info['version'], 'version ok');
		$this->assertEquals('stable', $info['stability'], 'stability ok');
		
		$info = $this->reflector->getPearVersionAndStability('0.2.1beta');
		$this->assertEquals('0.2.1', $info['version'], 'version ok');
		$this->assertEquals('beta', $info['stability'], 'stability ok');
		
		$info = $this->reflector->getPearVersionAndStability('1.0.0RC1');
		$this->assertEquals('1.0.0RC1', $info['version'], 'version ok');
		$this->assertEquals('beta', $info['stability'], 'stability ok');
	}
	
	/**
	 * @expectedException Exception
	 */
	public function testGetPearVersionAndStabilityWithBadVersion()
	{
		// we require 3 parts to our version
		$this->reflector->getPearVersionAndStability('0.3');
	}
	
	/**
	 * @expectedException Exception
	 */
	public function testGetPearVersionAndStabilityWithBadVersion2()
	{
		// bad stability string
		$this->reflector->getPearVersionAndStability('0.3.1gamma');
	}
}