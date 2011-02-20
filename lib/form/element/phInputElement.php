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
 * This class provides handling for basic "input" elements 
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage element
 */
class phInputElement extends phSimpleXmlElement
{
	public function getRawValue()
	{
		$e = $this->getElement();
		return (string)$e->attributes()->value;
	}
	
	public function setRawValue($value)
	{
		$e = $this->getElement();
		if(!isset($e->attributes()->value))
		{
			// has no value attribute we need to add it
			$e->addAttribute('value', (string)$value);
		}
		else
		{
			$e->attributes()->value = (string)$value;
		}
	}
}
