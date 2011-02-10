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
 * Simple element for testing purposes
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage test
 */
class phSimpleTestElement implements phFormViewElement
{
	public $boundDataItem = null;

	/**
	 * (non-PHPdoc)
	 * @see lib/form/phFormViewElement::needsUniqueName()
	 */
	public function createDataCollection()
	{
		return new phSimpleDataCollection();
	}

	/**
	 * (non-PHPdoc)
	 * @see lib/form/phFormViewElement::bindDataItem()
	 */
	public function bindDataItem(phFormDataItem $item)
	{
		$this->boundDataItem = $item;
	}
}