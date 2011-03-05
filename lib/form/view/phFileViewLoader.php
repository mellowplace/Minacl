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
	private $_viewsFolder = null;
	
	public function __construct($viewsFolder)
	{
		$this->_viewsFolder = $viewsFolder;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/view/phViewLoader::loadViewContents()
	 */
	public function getViewFileOrStream($view)
	{
		$info = $this->getFileAndDir($view);
		
		$dir = realpath($this->_viewsFolder . DIRECTORY_SEPARATOR . $info['dir']);
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