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
 * This class provides a common interface for any collection that is
 * to be used in the phCompositeDataCollection class
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
	 * @param phCompositeDataCollection $collection all the currently registered data items will be in here
	 */
	public function register(phFormViewElement $element, phNameInfo $name, phCompositeDataCollection $collection);
	
	/**
	 * Gives a chance for all currently registered collections in the composite to
	 * throw an exception if the element in the form is in an illegal configuration
	 * 
	 * @param phFormViewElement $element
	 * @param phNameInfo $name
	 * @param phCompositeDataCollection $collection
	 * @throws phFormException if there is a problem with the element
	 */
	public function validate(phFormViewElement $element, phNameInfo $name, phCompositeDataCollection $collection);
	
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