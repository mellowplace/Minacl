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
 *
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage data.collection
 */
class phFormDataCollection implements phDataCollection
{
	protected $_forms = array();
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phSimpleDataCollection::register()
	 */
	public function register(phFormViewElement $element, phNameInfo $name)
	{
		if(!($element instanceof phForm))
		{
			throw new phFormException('This collection can only store phForm instances');
		}
		
		if($name->isArray())
		{
			throw new phFormException("The name {$name} is invalid, you cannot use array names with forms");
		}
		
		$this->_forms[$name->getName()] = $element;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phDataCollection::find()
	 */
	public function find($name)
	{
		if(!array_key_exists($name, $this->_forms))
		{
			return null;
		}
		
		return $this->_forms[$name];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phDataCollection::createIterator()
	 */
	public function createIterator()
	{
		return new ArrayIterator($this->_forms);
	}
}