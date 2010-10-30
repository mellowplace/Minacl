<?php
/**
 * The file view loader knows how to load views that are plain old PHP files.
 * You must give it the directory where the views are located.  It can handle
 * identifiers like "user/register" which means it would look in the user 
 * subdirectory for register.php
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage view
 */
class phFileViewLoader extends phViewLoader
{
	private $viewsFolder = null;
	
	public function __construct($viewsFolder)
	{
		$this->viewsFolder = $viewsFolder;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/view/phViewLoader::loadViewContents()
	 */
	public function getViewFileOrStream($view)
	{
		$info = $this->getFileAndDir($view);
		
		$dir = realpath($this->viewsFolder . DIRECTORY_SEPARATOR . $info['dir']);
		$file = $dir . DIRECTORY_SEPARATOR . $info['file'];
		if($dir===false || !file_exists($file))
		{
			throw new phFormException("The view '{$view}' could not be found");
		}
		
		return $file;
	}
	
	protected function getFileAndDir($view)
	{
		$dir = '';
		$file = $view;
		
		if(($last = strrpos($view, '/'))!==false)
		{
			$file = substr($view, $last + 1);
			$dir = substr($view, 0, $last);
		}
		
		return array(
			'file' => $file . '.php',
			'dir' => $dir
		);
	}
}