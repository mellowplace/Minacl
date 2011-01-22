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
 * This class can bind elements in a view that have an array style name
 * e.g. <input type="hidden" name="ids[]" ...
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage view.binder
 */
class phArrayDataElementBinder extends phFormViewElementBinder
{
	/**
	 * @var array an array of phFormDataItem objects that I have created
	 */
	protected $_dataItems = array();
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/view/phFormViewElementBinder::canBindFor()
	 */
	public function canBindFor(phNameInfo $name, phForm $form)
	{
		return $name->isArray();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/view/phFormViewElementBinder::createAndBindDataItems()
	 */
	public function createAndBindDataItems($elements, phNameInfo $name, phForm $form)
	{
		if(array_key_exists($name->getName(), $this->_dataItems))
		{
			$dataItem = $this->_dataItems[$name->getName()];
		}
		else
		{
			$dataItem = new phArrayFormDataItem($name->getName());
			$this->_dataItems[$name->getName()] = $dataItem;
		}
		
		foreach($elements as $e)
		{
			$d = $dataItem->registerArrayKeyString($name->getArrayKeyString());
			$e->bindDataItem($d);
		}
		
		return $dataItem;
	}
}