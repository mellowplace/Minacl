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
 * Specific data collection for checkbox elements.
 * 
 * Checkboxes are a bit of a special case because they are only posted back to
 * the webserver if they are checked.  This means when you use auto keys like
 * name="ids[]" you need handle them differently because there may be 10 checkboxes
 * with that name but if only 2 are checked when the form is posted you'll have an
 * array with 2 elements.  Try to bind this array to a normal phArrayFormDataItem
 * type and you'll end up with the wrong checkboxes being marked as checked.
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage data.collection
 */
class phCheckboxDataCollection extends phSimpleArrayDataCollection
{
	/**
	 * Used to store all the values of a checkbox on a name by name basis.
	 * From this we can throw errors if someone tries to register a non
	 * unique value for a name.
	 * 
	 * @var array
	 */
	protected $_checkboxValues = array();
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phSimpleDataCollection::register()
	 */
	public function register(phFormViewElement $element, phNameInfo $name, phCompositeDataCollection $collection)
	{
		parent::register($element, $name, $collection);
		
		if($element instanceof phCheckboxElement)
		{
			$this->_checkboxValues[$name->getFullName()][] = $element->getRawValue();
		}
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
		
		$item = $collection->find($nameString);
		
		if($hasAutoKey && $item!==null)
		{
			if(!($element instanceof phCheckboxElement) && $item instanceof phSimpleArrayDataItem)
			{
				// not ok - checkbox registered here previously and someone is trying to add another datatype to the same array
				throw new phFormException("Trying to mix checkboxes with auto keys with another type of element at {$name}");
			}
			
			if($element instanceof phCheckboxElement && !($item instanceof phSimpleArrayDataItem))
			{
				// not ok - checkbox with auto key trying to be registered to a normal array type
				throw new phFormException("Trying to mix checkboxes with auto keys with another type of element at {$name}");
			}
		}
		
		if(!($element instanceof phCheckboxElement))
		{
			return; // not interested at this point if it's not a checkbox
		}
		
		/*
		 * check unique values
		 */
		if(	array_key_exists($name->getFullName(), $this->_checkboxValues) &&
			in_array($element->getRawValue(), $this->_checkboxValues[$name->getFullName()])
		)
		{
			// value is not unique
			throw new phFormException("Duplicate value for checkbox with name {$name->getFullName()}");
		}
		
		if(!$name->isArray() && $item!==null)
		{
			throw new phFormException("Cannot register checkbox with name {$name->getFullName()}, an item of data with the same name already exists.  If you are trying to use multiple checkboxes with the same name then please note that these must have array names e.g. \"ids[]\"");
		}
	}
	
	protected function needsSimpleArrayType(phArrayKeyInfo $info, phFormViewElement $element)
	{
		return $info->isAutoKey();
	}
}