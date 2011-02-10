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

require_once 'phTestCase.php';
require_once realpath(dirname(__FILE__)) . '/../../lib/form/phLoader.php';
phLoader::registerAutoloader();
require_once realpath(dirname(__FILE__)) . '/../resources/phTestFormView.php';

/**
 * Base class for select list tests
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 */
abstract class phAbstractSelectListTest extends phTestCase
{
	/**
	 * @return phSelectListElement
	 */
	protected function createSelectListElement($name, $options, $multiple = false, $selectedOptions = array())
	{
		$html = "<select name=\"{$name}\" " . ($multiple ? 'multiple="multiple"':'') . ">";
		foreach($options as $value=>$desc)
		{
			$selected = in_array($value, $selectedOptions);
			$html .= "<option value=\"{$value}\" " . ($selected ? 'selected="selected"' : '') . ">{$desc}</option>";
		}
		$html .= "</select>";

		$e = new SimpleXMLElement($html);

		return new phSelectListElement($e, new phTestFormView());
	}
}