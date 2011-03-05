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
 * The view loader can get the contents of a view from an identifier like
 * 'myFormView'  - it is used by phFormView to find the views 
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage view
 */
abstract class phViewLoader
{
	/**
	 * @var phViewLoader
	 */
	private static $_instance = null;
	
	/**
	 * Gets the view loader
	 * @return phViewLoader
	 */
	public static function getInstance()
	{
		if(self::$_instance===null)
		{
			throw new phFormException('No view loader instance has been set. You\'ll probably want to use a phFileViewLoader instance.');
		}
		
		return self::$_instance;
	}
	
	public static function setInstance(phViewLoader $instance)
	{
		self::$_instance = $instance;
	}
	
	/**
	 * 
	 * @param $view string the identifier for the view
	 * @return string a file on the system or a stream pointing to the view
	 */
	public abstract function getViewFileOrStream($view);
}