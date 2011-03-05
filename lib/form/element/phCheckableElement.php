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
 * This class provides a base class for radio buttons and checkboxes 
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage element
 */
class phCheckableElement extends phInputElement
{
	public function setValue($value)
	{
		$e = $this->getElement();
		
		if($value==$this->getRawValue())
		{
			/*
			 * value being set to same as our elements
			 * value="" attribute, therefore we need to
			 * be marked as checked
			 */
			$this->checkOn();
		}
		else
		{
			/*
			 * make sure we are not checked
			 */
			$this->checkOff();
		}
	}
	
	/**
	 * @return boolean true if the checkbox is checked
	 */
	public function isChecked()
	{
		$e = $this->getElement();
		return (isset($e->attributes()->checked) && ((string)$e->attributes()->checked=='checked'));
	}
	
	/**
	 * Marks the checkbox as checked
	 */
	protected function checkOn()
	{
		$e = $this->getElement();
		unset($e->attributes()->checked);
		$e->addAttribute('checked', 'checked');
	}
	
	/**
	 * Marks the checkbox as unchecked
	 */
	protected function checkOff()
	{
		$e = $this->getElement();
		unset($e->attributes()->checked);
	}
}