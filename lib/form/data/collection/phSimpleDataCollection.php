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
 * Straight forward data collection for elements that need no special
 * rules 
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage data.collection
 */
class phSimpleDataCollection implements phDataCollection
{
	protected $_dataItems = array();
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phDataCollection::register()
	 */
	public function register(phFormViewElement $element, phNameInfo $name)
	{
		if($name->isArray())
		{
			$this->registerArray($element, $name);
		}
		else
		{
			$this->registerNormal($element, $name);
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
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phDataCollection::find()
	 */
	public function find($name)
	{
		if(!array_key_exists($name, $this->_dataItems))
		{
			return null;
		}
		
		return $this->_dataItems[$name];
	}
	
	protected function registerArray(phFormViewElement $element, phNameInfo $name)
	{
		if(!array_key_exists($name->getName(), $this->_dataItems))
		{
			$currentDataItem = new phArrayFormDataItem($name->getName());
			$this->_dataItems[$name->getName()] = $currentDataItem;
		}
		else
		{
			$currentDataItem = $this->_dataItems[$name->getName()];
			if(!($currentDataItem instanceof phArrayFormDataItem))
			{
				throw new phFormException("There is already a data item registered at {$name->getName()} and it is not an array data type so I cannot add to it!");
			}
		}
			
		$finalItem = $this->getOrCreateArrayDataType($name, $name->getArrayInfo()->getKeys(), 0, $element, $currentDataItem);
		$element->bindDataItem($finalItem);
	}
	
	protected function getOrCreateArrayDataType(phNameInfo $name, $keys, $currentKeyIndex, phFormViewElement $element, phArrayFormDataItem $currentDataItem)
	{
		$currentKey = $keys[$currentKeyIndex]->isAutoKey() ? $currentDataItem->getNextAutoKey() : $keys[$currentKeyIndex]->getKey();
		
		if($currentKeyIndex == (sizeof($keys) - 1))
		{
			// last key
			if(isset($currentDataItem[$currentKey]))
			{
				$finalItem = $currentDataItem[$currentKey];
			}
			else
			{
				$finalItem = new phFormDataItem($currentKey);
				$currentDataItem->registerArrayKey($keys[$currentKeyIndex], $finalItem);
			}
			
			return $finalItem;
		}
		else
		{
			if(isset($currentDataItem[$currentKey]))
			{
				$nextDataItem = $currentDataItem[$currentKey];
				if(!($nextDataItem instanceof phArrayFormDataItem))
				{
					$path = $name->getName();
					for($x=0; $x<=$currentKeyIndex; $x++)
					{
						$path .= "[{$keys[$currentKeyIndex]->getKey()}]";
					}
					throw new phFormException("Unable to register array type data at {$path} is not an array data type");
				}
			}
			else
			{
				$nextDataItem = new phArrayFormDataItem($keys[$currentKeyIndex]->getKey());
				$currentDataItem->registerArrayKey($keys[$currentKeyIndex], $nextDataItem);
			}
			
			return $this->getOrCreateArrayDataType($name, $keys, ++$currentKeyIndex, $element, $nextDataItem);
		}
	}
	
	protected function registerNormal(phFormViewElement $element, phNameInfo $name)
	{
		if(array_key_exists($name->getName(), $this->_dataItems))
		{
			throw new phFormException("Cannot register normal data at {$name->getName()}, there is already a data item registered there");
		}
		
		$data = new phFormDataItem($name->getName());
		$this->_dataItems[$name->getName()] = $data;
		$element->bindDataItem($data);
	}
}