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
 * This class acts as an autoloader for the Minacl framework.  It should have its 
 * registerAutoloader function called by anyone who wants to use this library
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 */
class phLoader
{
	/**
	 * This method sets up the autoloader so all the classes to do with this library 
	 * get loaded when needed 
	 */
	public static function registerAutoloader()
	{
		spl_autoload_register(array('phLoader', 'autoloadClass'));
	}
	
	/**
	 * Autoloading class that is registered by calling phForm::registerAutoloader()
	 * 
	 * @param string $className
	 */
	public static function autoloadClass($className)
	{
		if(isset(self::$_classes[$className]))
		{
			require_once realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . self::$_classes[$className];
		}
	}
	
	/**
	 * An associative array that holds a list of all classes this library provides
	 * and their relative path from this class.  It's used by the autoloader to
	 * load the classes of the library.
	 * 
	 * @var array
	 */
	private static $_classes = array(
		'phForm'						=> 'phForm.php',
		'phFormViewElement'				=> 'phFormViewElement.php',
		'phFormException'				=> 'phFormException.php',
		'phFormView'					=> 'view/phFormView.php',
		'phViewLoader'					=> 'view/phViewLoader.php',
		'phFileViewLoader'				=> 'view/phFileViewLoader.php',
		'phNameInfo'					=> 'view/phNameInfo.php',
		'phArrayInfo'					=> 'view/phArrayInfo.php',
		'phArrayKeyInfo'				=> 'view/phArrayInfo.php',
		'phData'						=> 'data/phData.php',
		'phFormDataItem'				=> 'data/phFormDataItem.php',
		'phArrayFormDataItem'			=> 'data/phArrayFormDataItem.php',
		'phSimpleArrayDataItem'			=> 'data/phSimpleArrayDataItem.php',
		'phFileFormDataItem'			=> 'data/phFileFormDataItem.php',
		'phDataChangeListener'			=> 'data/phDataChangeListener.php',
		'phFileDataException'			=> 'data/phFileDataException.php',
		'phAbstractFindingCollection'	=> 'data/collection/phAbstractFindingCollection.php',
		'phDataCollection'				=> 'data/collection/phDataCollection.php',
		'phSimpleDataCollection'		=> 'data/collection/phSimpleDataCollection.php',
		'phCompositeDataCollection'		=> 'data/collection/phCompositeDataCollection.php',
		'phFormDataCollection'			=> 'data/collection/phFormDataCollection.php',
		'phFileDataCollection'			=> 'data/collection/phFileDataCollection.php',
		'phCheckboxDataCollection'		=> 'data/collection/phCheckboxDataCollection.php',
		'phRadioDataCollection'			=> 'data/collection/phRadioDataCollection.php',
		'phSimpleArrayDataCollection'	=> 'data/collection/phSimpleArrayDataCollection.php',
		'phSelectListDataCollection'	=> 'data/collection/phSelectListDataCollection.php',
		'phValidatable'					=> 'phValidatable.php',
		'phFormException'				=> 'phFormException.php',
		'phElementFactory'				=> 'factory/phElementFactory.php',
		'phInputElement'				=> 'element/phInputElement.php',
		'phSimpleXmlElement'			=> 'element/phSimpleXmlElement.php',
		'phCheckableElement'			=> 'element/phCheckableElement.php',
		'phCheckboxElement'				=> 'element/phCheckboxElement.php',
		'phRadioButtonElement'			=> 'element/phRadioButtonElement.php',
		'phFileElement'					=> 'element/phFileElement.php',
		'phSelectListElement'			=> 'element/phSelectListElement.php',
		'phValidator'					=> 'validator/phValidator.php',
		'phValidatorCommon'				=> 'validator/phValidatorCommon.php',
		'phValidatorException'			=> 'validator/phValidatorException.php',
		'phValidatorLogic'				=> 'validator/phValidatorLogic.php',
		'phStringLengthValidator'		=> 'validator/phStringLengthValidator.php',
		'phRequiredValidator'			=> 'validator/phRequiredValidator.php',
		'phCompareValidator'			=> 'validator/phCompareValidator.php',
		'phFileValidator'				=> 'validator/phFileValidator.php',
		'phValidatorError'				=> 'validator/phValidatorError.php',
		'phEmailValidator'				=> 'validator/phEmailValidator.php',
		'phTextAreaElementFactory'		=> 'factory/phTextAreaElementFactory.php',
		'phTextAreaElement' 			=> 'element/phTextAreaElement.php',
	);
}