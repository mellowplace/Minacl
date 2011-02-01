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
 * Collection for handling phForm instances
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage data.collection
 */
class phFormDataCollection extends phAbstractFindingCollection
{
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phSimpleDataCollection::register()
	 */
	public function register(phFormViewElement $element, phNameInfo $name, phCompositeDataCollection $collection)
	{
		if(!($element instanceof phForm))
		{
			throw new phFormException('This collection can only store phForm instances');
		}
		
		if($name->isArray())
		{
			throw new phFormException("The name {$name} is invalid, you cannot use array names with forms");
		}
		
		$this->_dataItems[$name->getName()] = $element;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phDataCollection::validate()
	 */
	public function validate(phFormViewElement $element, phNameInfo $name, phCompositeDataCollection $collection)
	{
		if($element instanceof phForm)
		{
			$data = $collection->find($name->getFullName());
			if($data !== null)
			{
				throw new phFormException("There is already a data item registered with the name {$name->getFullName()}");
			}
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phDataCollection::createIterator()
	 */
	public function createIterator()
	{
		return new ArrayIterator($this->_dataItems);
	}
}