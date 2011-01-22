<?php
/*
 * phForms Project: An HTML forms library for PHP
 *          https://github.com/mellowplace/PHP-HTML-Driven-Forms/
 * Copyright (c) 2010, 2011 Rob Graham
 * 
 * This file is part of phForms.
 *
 * phForms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version.
 *
 * phForms is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public 
 * License along with phForms.  If not, see 
 * <http://www.gnu.org/licenses/>.
 */

/**
 * A concrete version of this class knows how to create phFormDataItem objects
 * from an array of phFormViewElement objects and bind the data to the elements
 * for a given piece of data defined in a view
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage view
 */
class phFormViewElementBinder
{
	private $_binders = array();
	
	/**
	 * Creates a binder instance that is a composite of concrete classes that do the actual
	 * work of binding for the correct phNameInfo
	 * 
	 * @param phNameInfo $name
	 * @return phFormViewElementBinder
	 */
	public static function createInstance()
	{
		return new phFormViewElementBinder(self::loadBinders());
	}
	
	private static function loadBinders()
	{
		$binders = array();
		
		$dir = realpath(dirname(__FILE__) . '/binder');
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
					$binders[] = $class->newInstanceArgs();
				}
			}
		}
		
		return $binders;
	}
	
	public function __construct($binders = array())
	{
		$this->_binders = $binders;
	}
	
	/**
	 * @param phNameInfo $name
	 * @param phForm $form the form that we are doing the binding for
	 * @return boolean true if the implementing object can bind data to elements for the given name info
	 */
	public function canBindFor(phNameInfo $name, phForm $form)
	{
		foreach($this->_binders as $b)
		{
			if($b->canBindFor($name, $form))
			{
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Given an array of phFormViewElement objects this method binds the corresponding
	 * phFormDataItem object to each of them
	 * 
	 * @param array $elements
	 * @param phNameInfo info about the original name="" attribute of the element in the view
	 * @param phForm $form the form that we are doing the binding for
	 * @return phFormDataItem
	 */
	public function createAndBindDataItems($elements, phNameInfo $name, phForm $form)
	{
		foreach($this->_binders as $b)
		{
			if($b->canBindFor($name, $form))
			{
				return $b->createAndBindDataItems($elements, $name, $form);
			}
		}
		
		throw new phFormException("I have no element binder available for the data item {$name->getName()}");
	}
}