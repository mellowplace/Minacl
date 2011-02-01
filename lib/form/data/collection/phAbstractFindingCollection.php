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
 * Provides a base for collections that need finding functionality
 * particularly around arrays
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage data.collection
 */
abstract class phAbstractFindingCollection implements phDataCollection
{
	protected $_dataItems = array();
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phDataCollection::find()
	 */
	public function find($name)
	{
		$info = new phNameInfo($name);
		
		if($info->isArray())
		{
			// if the $name specifies any auto keys then they will not be able to be found
			$keys = $info->getArrayInfo()->getKeys();
			foreach($keys as $k)
			{
				if($k->isAutoKey())
				{
					throw new phFormException("{$name} is ambiguous as it specifies an auto key ([])");
				}
			}
		}
		
		if(!array_key_exists($info->getName(), $this->_dataItems))
		{
			return null;
		}
		
		$dataItem = $this->_dataItems[$info->getName()];
		if($info->isArray() && !($dataItem instanceof phArrayFormDataItem))
		{
			/*
			 * name is an array but the data type registered there is not
			 * an array type
			 */
			return null;
		}
		
		if($info->isArray())
		{
			$dataItem = $this->recurseArray($dataItem, $info->getArrayInfo()->getKeys());
		}
		
		return $dataItem;
	}
	
	protected function recurseArray($item, $keys, $currentKey = 0)
	{
		if(!($item instanceof phArrayFormDataItem))
		{
			return null;
		}
		
		$keyString = $keys[$currentKey]->getKey();
		
		if(!isset($item[$keyString]))
		{
			return null;
		}
		
		if($currentKey == (sizeof($keys)-1))
		{
			// on the last key
			return $item[$keyString];
		}
		else
		{
			return $this->recurseArray($item[$keyString], $keys, ++$currentKey);
		}
	}
}