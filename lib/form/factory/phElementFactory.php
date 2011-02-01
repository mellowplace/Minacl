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

/**
 * The element factory finds the appropriate creator of phElement instances
 * for a given SimpleXmlElement object
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage factory
 */
abstract class phElementFactory
{
	private static $_factories = null;
	
	/**
	 * Gets the correct phElementFactory instance for creating
	 * the right phElement for $e
	 * @param SimpleXMLElement $e
	 * @return phElementFactory
	 */
	public static function getFactory(SimpleXMLElement $e)
	{
		if(self::$_factories===null)
		{
			self::$_factories = self::loadFactories();
		}
		
		foreach(self::$_factories as $f)
		{
			if($f->canHandle($e))
			{
				return $f;
			}
		}
	}
	
	private static function loadFactories()
	{
		$factories = array();
		
		$dir = realpath(dirname(__FILE__));
		$files = new DirectoryIterator($dir);
		foreach($files as $f)
		{
			$filename = $f->getFilename();
			if($f->isFile() && strlen($filename)>4 && substr($filename, -4)=='.php')
			{
				require_once($dir .'/' . $filename);
				$className = substr($filename, 0, -4);
				$class = new ReflectionClass($className);
				
				if($class->isInstantiable())
				{
					$factories[] = $class->newInstanceArgs();
				}
			}
		}
		
		return $factories;
	}
	
	public abstract function canHandle(SimpleXMLElement $e);
	
	/**
	 * creates a new phElement from a SimpleXMLElement
	 * 
	 * @param SimpleXMLElement $e
	 * @param phFormView $view the view the element appears on
	 * @return phFormViewElement
	 */
	public abstract function createPhElement(SimpleXMLElement $e, phFormView $view);
}