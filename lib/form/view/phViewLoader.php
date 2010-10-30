<?php
/**
 * The view loader can get the contents of a view from an identifier like
 * 'myFormView'  - it is used by phFormView to find the views 
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage view
 */
abstract class phViewLoader
{
	/**
	 * @var phViewLoader
	 */
	private static $instance = null;
	
	/**
	 * Gets the view loader
	 * @return phViewLoader
	 */
	public static function getInstance()
	{
		if(self::$instance===null)
		{
			throw new phFormException('No view loader instance has been set. You\'ll probably want to use a phFileViewLoader instance.');
		}
		
		return self::$instance;
	}
	
	public function setInstance(phViewLoader $instance)
	{
		self::$instance = $instance;
	}
	
	/**
	 * 
	 * @param $view string the identifier for the view
	 * @return string a file on the system or a stream pointing to the view
	 */
	public abstract function getViewFileOrStream($view);
}