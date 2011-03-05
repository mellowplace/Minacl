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
 * This represents some data, it can have values bound to it and 
 * can be cleared of data.  It can also be validated.
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage data
 */
interface phData extends phValidatable
{
	/**
	 * @return string a name to identify this data with
	 */
	public function getName();
	
	/**
	 * Binds a value to the data
	 * @param $values mixed the data to be bound
	 */
	public function bind($values);
	
	/**
	 * clears any bound values
	 */
	public function clear();
	
	/**
	 * Gets the value of the data
	 * 
	 * @return mixed
	 */
	public function getValue();
	
	/**
	 * Set a validator to validate what is bound to this object
	 * 
	 * @param phValidator $validator
	 */
	public function setValidator(phValidator $validator);
	
	/**
	 * @return phValidator
	 */
	public function getValidator();
}