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
 * The data collection interface creates data items for elements registered with it
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage data.collection
 */
interface phDataCollection
{
	/**
	 * Registers an element with this collection
	 * 
	 * @param phFormViewElement $element
	 * @param phNameInfo $name
	 */
	public function register(phFormViewElement $element, phNameInfo $name);
	
	/**
	 * @return Iterator an iterator for going over the phData objects in this collection
	 */
	public function createIterator();
	
	/**
	 * Gets a phData item referred to by $name
	 * 
	 * @param string $name
	 * @return phData
	 */
	public function find($name);
}