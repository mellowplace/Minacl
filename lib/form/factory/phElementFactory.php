<?php
/**
 * The element factory finds the appropriate creator of phElement instances
 * for a given SimpleXmlElement object
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage factory
 */
abstract class phElementFactory
{
	private static $_factories = null;
	
	public static function getFactory(SimpleXMLElement $e)
	{
		if(self::$_factories===null)
		{
			self::$_factories = self::loadFactories();
		}
		
		foreach(self::$_factories as $f)
		{
			if($f->canHandle($e))
			{
				return $f;
			}
		}
	}
	
	private static function loadFactories()
	{
		$factories = array();
		
		$dir = realpath(dirname(__FILE__));
		$files = new DirectoryIterator($dir);
		foreach($files as $f)
		{
			$filename = $f->getFilename();
			if($f->isFile() && strlen($filename)>4 && substr($filename, -4)=='.php')
			{
				require_once($dir .'/' . $filename);
				$className = substr($filename, 0, -4);
				$class = new ReflectionClass($className);
				
				if($class->isInstantiable())
				{
					$factories[] = $class->newInstanceArgs();
				}
			}
		}
		
		return $factories;
	}
	
	public abstract function canHandle(SimpleXMLElement $e);
	
	/**
	 * creates a new phElement from a SimpleXMLElement
	 * 
	 * @param SimpleXMLElement $e
	 * @param phFormView $view the view the element appears on
	 * @return phElement
	 */
	public abstract function createPhElement(SimpleXMLElement $e, phFormView $view);
}