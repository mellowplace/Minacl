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
 * Knows how to deal with <select ...> tags
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage element
 */
class phSelectListElement extends phSimpleXmlElement
{
	/**
	 * @return boolean true if the select is a multi select
	 */
	public function isMultiple()
	{
		$e = $this->getElement();
		return (isset($e->attributes()->multiple) && ((string)$e->attributes()->multiple=='multiple'));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/element/phSimpleXmlElement::getRawValue()
	 */
	public function getRawValue()
	{
		$e = $this->getElement();
		$selectedOptions = $e->xpath("./option[@selected='selected']");
		
		if($this->isMultiple())
		{
			$values = array();
			foreach($selectedOptions as $o)
			{
				$values[] = (string)$o->attributes()->value;
			}
			
			return $values;
		}
		else
		{
			return isset($selectedOptions[0]) ? (string)$selectedOptions[0]->attributes()->value : null;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/element/phSimpleXmlElement::setRawValue()
	 */
	public function setRawValue($value)
	{
		if(is_array($value) && !$this->isMultiple())
		{
			// cannot set an array with a single select
			throw new phFormException('Cannot set an array value for a non-multiple select list');
		}
		
		if($this->isMultiple() && !is_array($value))
		{
			// multiple selects need an array
			throw new phFormException('You must set an array value for a multiple select list');
		}
		
		$e = $this->getElement();
		$options = $e->children();
		
		foreach($options as $o)
		{
			$oVal = (string)$o->attributes()->value;
			if(is_array($value))
			{
				if(in_array($oVal, $value))
				{
					$o->addAttribute('selected', 'selected');
				}
				else
				{
					unset($o->attributes()->selected);
				}
			}
			else
			{
				if($oVal==$value)
				{
					$o->addAttribute('selected', 'selected');
				}
				else 
				{
					unset($o->attributes()->selected);
				}
			}
		}
	}
	
	public function createDataCollection()
	{
		return new phSelectListDataCollection();
	}
}