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
 * A data collection that stores radio buttons data
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage data.collection
 */
class phRadioDataCollection extends phSimpleDataCollection
{
	protected $_radioValues = array();
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phSimpleDataCollection::register()
	 */
	public function register(phFormViewElement $element, phNameInfo $name, phCompositeDataCollection $collection)
	{
		parent::register($element, $name, $collection);
		
		if($element instanceof phRadioButtonElement)
		{
			$this->_radioValues[$name->getFullName()][] = $element->getRawValue();
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phSimpleDataCollection::validate()
	 */
	public function validate(phFormViewElement $element, phNameInfo $name, phCompositeDataCollection $collection)
	{
		if($name->isArray())
		{
			$arrayInfo = $name->getArrayInfo();
			$keys = $arrayInfo->getKeys();
			$lastKey = array_pop($keys);
			
			if($lastKey->isAutoKey())
			{
				throw new phFormException("The name {$name->getFullName()} is invalid, you cannot use an auto key with a radio button");
			}
		}
		
		if(!($element instanceof phRadioButtonElement))
		{
			return; // not interested from here on
		}
		
		if(array_key_exists($name->getFullName(), $this->_radioValues))
		{
			if(in_array($element->getRawValue(), $this->_radioValues[$name->getFullName()]))
			{
				throw new phFormException("Non unique value for radio button with name \"{$name->getFullName()}\"");
			}
		}
	}
	
	/**
	 * Changed behavior so if an element with the same name has already been registered then that
	 * same data item created last time is bound to this element
	 * @see lib/form/data/collection/phSimpleDataCollection::registerNormal()
	 */
	protected function registerNormal(phFormViewElement $element, phNameInfo $name, phCompositeDataCollection $collection)
	{
		if(!array_key_exists($name->getName(), $this->_dataItems))
		{
			$item = $this->createNormalDataItem($name->getName(), $element);
			$this->_dataItems[$name->getName()] = $item;
		}
		
		$item = $this->_dataItems[$name->getName()];
		$element->bindDataItem($item); 
	}
}