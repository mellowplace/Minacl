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
 * This class provides handling for checkboxes 
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage element
 */
class phCheckboxElement extends phCheckableElement
{
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phDataChangeListener::dataChanged()
	 */
	public function dataChanged(phFormDataItem $item)
	{
		if($item instanceof phSimpleArrayDataItem)
		{
			/*
			 * $item->getValue will be an array, we need to see if our 
			 * value is in it - if so set ourselves to checked by calling
			 * $this->setValue
			 */
			$values = $item->getValue();
			$ourValue = $this->getRawValue();
			if(in_array($ourValue, $values))
			{
				$this->checkOn();
			}
			else
			{
				$this->checkOff();
			}
		}
		else
		{
			parent::dataChanged($item);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/element/phSimpleXmlElement::createDataCollection()
	 */
	public function createDataCollection()
	{
		return new phCheckboxDataCollection();
	}
}
