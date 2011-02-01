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
 * This data item is used to represent any array type data specified in a form
 * e.g. <input type="checkbox" name="ids[]" value="1" />
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage data
 */
class phCheckboxArrayDataItem extends phArrayFormDataItem
{
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/phArrayFormDataItem::registerArrayKey()
	 */
	public function registerArrayKey(phArrayKeyInfo $key, phData $dataItem)
	{
		throw new phFormException("You cannot register array keys with this object, it is only for handling the special case of chekboxes with auto keys");
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/phArrayFormDataItem::bind()
	 */
	public function bind($value)
	{
		if($values===null)
		{
			$values = array();
		}
		
		if(!is_array($value))
		{
			throw new phFormException("Trying to bind a value that is not an array to {$this->_name}");
		}
		
		$this->_value = $value;
		
		foreach($this->_listeners as $l)
		{
			$l->dataChanged($this);
		}
	}
		
}
