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
 * A phSimpleXmlElement is a decorator for SimpleXMLElement objects that allows a html tag like "input"
 * to be an element in a phForm
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage element
 */
abstract class phSimpleXmlElement implements phDataChangeListener, phFormViewElement
{
	/**
	 * The view this element appears on
	 * @var phFormView
	 */
	protected $_view = null;
	
	public function __construct(SimpleXMLElement $element, phFormView $view)
	{
		$this->_element = $element;
		$this->_view = $view;
	}
	
	public function setValue($value)
	{
		$this->setRawValue($value);
	}
	
	/**
	 * gets the raw value of the SimpleXmlElement element
	 */
	public abstract function getRawValue();
	
	/**
	 * sets the raw value of the SimpleXmlElement element
	 * @param mixed $value
	 */
	public abstract function setRawValue($value);
	
	/**
	 * @return SimpleXMLElement
	 */
	public function getElement()
	{
		return $this->_element;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phDataChangeListener::dataChanged()
	 */
	public function dataChanged(phFormDataItem $item)
	{
		$this->setValue($item->getValue());
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phFormViewElement::bindDataItem()
	 */
	public function bindDataItem(phFormDataItem $item)
	{
		$item->addChangeListener($this);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phFormViewElement::canAppearMultipleTimesWithSameName()
	 */
	public function createDataCollection()
	{
		return new phSimpleDataCollection();
	}
}