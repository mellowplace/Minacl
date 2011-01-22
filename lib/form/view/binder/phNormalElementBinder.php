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
 * This class can bind normal elements in a view i.e. not forms
 * and not arrays
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage view.binder
 */
class phNormalElementBinder extends phFormViewElementBinder
{
	/**
	 * (non-PHPdoc)
	 * @see lib/form/view/phFormViewElementBinder::canBindFor()
	 */
	public function canBindFor(phNameInfo $name, phForm $form)
	{
		return (!$form->hasForm($name->getName()) && !$name->isArray());
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/view/phFormViewElementBinder::createAndBindDataItems()
	 */
	public function createAndBindDataItems($elements, phNameInfo $name, phForm $form)
	{
		$dataItem = null; // new phFormDataItem($name->getName());
		
		foreach($elements as $e)
		{
			$class = $e->getDataItemClassName();
			if($dataItem===null)
			{
				$dataItem = new $class($name->getName());
			}
			else if(strtolower(get_class($dataItem)) != strtolower($class))
			{
				/*
				 * someones trying to mix elements that need different phFormDataItem
				 * classes. Not allowed!
				 */
				throw new phFormException("There are multiple elements registered for the name \"{$name->getName()}\" that use different data types. You cannot mix elements that need different data types, please rename.");
			}
			$e->bindDataItem($dataItem);
		}
		
		return $dataItem;
	}
}