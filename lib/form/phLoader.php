<?php
/**
 * This class acts as an autoloader for the phforms framework.  It should have its 
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
		if(isset(self::$classes[$className]))
		{
			require_once realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . self::$classes[$className];
		}
	}
	
	/**
	 * An associative array that holds a list of all classes this library provides
	 * and their relative path from this class.  It's used by the autoloader to
	 * load the classes of the library.
	 * 
	 * @var array
	 */
	private static $classes = array(
		'phForm'					=> 'phForm.php',
		'phFormViewElement'			=> 'phFormViewElement.php',
		'phFormException'			=> 'phFormException.php',
		'phFormView'				=> 'view/phFormView.php',
		'phViewLoader'				=> 'view/phViewLoader.php',
		'phFileViewLoader'			=> 'view/phFileViewLoader.php',
		'phData'					=> 'phData.php',
		'phValidatable'				=> 'phValidatable.php',
		'phFormDataItem'			=> 'phFormDataItem.php',
		'phDataChangeListener'		=> 'phDataChangeListener.php',
		'phFormException'			=> 'phFormException.php',
		'phElementFactory'			=> 'factory/phElementFactory.php',
		'phInputElement'			=> 'element/phInputElement.php',
		'phSimpleXmlElement'		=> 'element/phSimpleXmlElement.php',
		'phCheckboxElement'			=> 'element/phCheckboxElement.php',
		'phValidator'				=> 'validator/phValidator.php',
		'phValidatorCommon'			=> 'validator/phValidatorCommon.php',
		'phValidatorException'		=> 'validator/phValidatorException.php',
		'phValidatorLogic'			=> 'validator/phValidatorLogic.php',
		'phStringLengthValidator'	=> 'validator/phStringLengthValidator.php',
		'phRequiredValidator'		=> 'validator/phRequiredValidator.php',
		'phCompareValidator'		=> 'validator/phCompareValidator.php',
		'phTextAreaElementFactory'  => 'factory/phTextAreaElementFactory.php',
		'phTextAreaElement' 		=> 'element/phTextAreaElement.php',
	);
}