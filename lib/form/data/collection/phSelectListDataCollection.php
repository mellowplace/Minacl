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
class phSelectListDataCollection extends phSimpleArrayDataCollection
{
	
	public function validate(phFormViewElement $element, phNameInfo $name, phCompositeDataCollection $collection)
	{
		parent::validate($element, $name, $collection);
		
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
		
		if($element instanceof phSelectListElement && $element->isMultiple() && !$hasAutoKey)
		{
			throw new phFormException("Invalid name \"{$name->getFullName()}\" - Multi select list elements must use a name with an auto key");
		}
		
		$item = $collection->find($nameString);
		
		/**
		 * @todo 
		 * 
		 * 1. If a we already have a multi select autokey checkbox at $name check no other element
		 * is trying to register there we must throw an exception
		 * 
		 * 2. If we are trying to register a multi select autokey but there exists another data item there
		 * then we must throw an exception
		 */
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