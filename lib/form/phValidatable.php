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
 * Something that is "validatable" can have errors attached to it and be validated
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 */
interface phValidatable
{
	public function validate();
	/**
	 * allows a validator to attach an error message to this element
	 * @param phValidatorError $error
	 */
	public function addError(phValidatorError $error);
	
	/**
	 * resets any error messages this element might have
	 */
	public function resetErrors();
	
	/**
	 * gets any error messages that have been added to this element
	 * @return array
	 */
	public function getErrors();
}