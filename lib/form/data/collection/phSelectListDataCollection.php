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
 *
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage data.collection
 */
class phSelectListDataCollection extends phSimpleArrayDataCollection
{
	/**
	 * Stores the select list elements that have been registered and are multi arrays
	 * @var array
	 */
	protected $_multipleElements = array();
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phSimpleDataCollection::register()
	 */
	public function register(phFormViewElement $element, phNameInfo $name, phCompositeDataCollection $collection)
	{
		if($element instanceof phSelectListElement && $element->isMultiple())
		{
			$this->_multipleElements[$name->getFullName()] = $element;
		}
		
		parent::register($element, $name, $collection);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phSimpleDataCollection::validate()
	 */
	public function validate(phFormViewElement $element, phNameInfo $name, phCompositeDataCollection $collection)
	{
		/*
		 * go through the name and, if it's an array, build it back up, BUT - skip
		 * the last key if it's an auto key.  We do this because checkboxes with auto 
		 * key's cannot be mixed with any other data type.
		 */
		$nameString = $name->getName();
		$hasAutoKey = false;
		
		if($name->isArray())
		{
			$keys = $name->getArrayInfo()->getKeys();
			foreach($keys as $k)
			{
				if($k->isAutoKey())
				{
					$hasAutoKey = true;
					break;
				}
				$nameString .= "[{$k->getKey()}]";
			}
		}
		
		if($element instanceof phSelectListElement && $element->isMultiple())
		{
			if(!$hasAutoKey)
			{
				throw new phFormException("Invalid name \"{$name->getFullName()}\" - Multi select list elements must use a name with an auto key");
			}
			else 
			{
				$data = $collection->find($nameString);
				if($data!==null)
				{
					throw new phFormException("Cannot register multi-select list at {$name->getFullName()} another data item exists there");
				}
			}
		}
		
		if(array_key_exists($name->getFullName(), $this->_multipleElements))
		{
			throw new phFormException("There is a multi-select list registered at \"{$name->getFullName()}\", you cannot register any other types of data here");
		}
		
		if($element instanceof phSelectListElement)
		{
			/*
			 * also do uniqueness checks if we are registering a select element
			 */
			parent::validate($element, $name, $collection);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phSimpleArrayDataCollection::needsSimpleArrayType()
	 */
	public function needsSimpleArrayType(phArrayKeyInfo $info, phFormViewElement $element)
	{
		/*
		 * if the select list is a multi select and the key is an auto key then
		 * we'll need the simple array type
		 */
		return ($element instanceof phSelectListElement && $element->isMultiple() && $info->isAutoKey());
	}
}