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
 * This is a base class for any collection that in some circumstances will want to
 * use a simple array instead of the normal array type - checkboxes and select lists
 * need this behavior in some situations when auto keys are involved
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage data.collection
 */
abstract class phSimpleArrayDataCollection extends phSimpleDataCollection
{
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phSimpleDataCollection::getOrCreateArrayDataType()
	 */
	protected function getOrCreateArrayDataType(phNameInfo $name, $keys, $currentKeyIndex, phFormViewElement $element, phArrayFormDataItem $currentDataItem)
	{
		if($currentDataItem instanceof phSimpleArrayDataItem)
		{
			/*
			 * a simple array data type has been created or gotten. This
			 * means we are at the end of the array and cannot go any 
			 * further down so we should return this data item.
			 */
			return $currentDataItem;
		}
		else
		{
			return parent::getOrCreateArrayDataType($name, $keys, $currentKeyIndex, $element, $currentDataItem);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phSimpleDataCollection::createArrayDataItem()
	 */
	protected function createArrayDataItem(phArrayKeyInfo $info, phFormViewElement $element, $name = null)
	{
		if($name===null)
		{
			$name = $info->getKey();
		}
		
		if($this->needsSimpleArrayType($info, $element))
		{
			return new phSimpleArrayDataItem($name);
		}
		else
		{
			return new phArrayFormDataItem($name);
		}
	}
	
	/**
	 * Called when creating array data items to see if phSimpleArrayDataItem is needed
	 * 
	 * @param phArrayKeyInfo $info
	 * @param phFormViewElement $element
	 * @return boolean true if a simple array type is needed as opposed to a normal composite array type
	 */
	protected abstract function needsSimpleArrayType(phArrayKeyInfo $info, phFormViewElement $element);
}