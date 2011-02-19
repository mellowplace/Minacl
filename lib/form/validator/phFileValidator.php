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
 * Validates uploaded files
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage validator
 */
class phFileValidator extends phValidatorCommon
{
	const REQUIRED = 1;
	const FILE_ERROR = 2;
	const INVALID_MIME_TYPE = 3;
	
	/**
	 * Is the file required?
	 * 
	 * @var boolean
	 */
	protected $_required = false;
	
	/**
	 * Filled if we require a certain mime type
	 * 
	 * @var array
	 */
	protected $_mimeTypes = array();
	
	protected function getValidErrorCodes()
	{
		return array(self::REQUIRED, self::FILE_ERROR, self::INVALID_MIME_TYPE);
	}
	
	protected function getDefaultErrorMessages()
	{
		return array(
			self::REQUIRED=>'This file is required, please upload something',
			self::FILE_ERROR=>'There was an error while trying to upload the file, the error was: %error%',
			self::INVALID_MIME_TYPE=>'Incorrect file type, valid types are: %types%',
		);
	}
	
	public function validate($value, phValidatable $errors)
	{
		if($this->_required && $value===null)
		{
			$errors->addError($this->getError(self::REQUIRED));
			return;
		}
		
		if($value===null)
		{
			return; // go no further, no file passed
		}
		
		$data = new phFileFormDataItem('parse');
		try
		{
			$data->bind($value);
		}
		catch(phFileDataException $e)
		{
			$errors->addError($this->getError(self::FILE_ERROR, array('%error%'=>'invalid file data')));
			return; // no point in going further, bad data
		}
		
		if($this->_required && !file_exists($data->getTempFileName()))
		{
			$errors->addError($this->getError(self::REQUIRED));
			return;
		}
		
		if($data->hasError())
		{
			$errors->addError($this->getError(self::FILE_ERROR, array('%error%'=>$data->getFileErrorString())));
			return; // no point in going further, bad data
		}
		
		if(sizeof($this->_mimeTypes)>0 && !in_array($data->getMimeType(), $this->_mimeTypes))
		{
			$validTypes = '';
			foreach($this->_mimeTypes as $m)
			{
				$validTypes .= $m . ', ';
			}
			$errors->addError($this->getError(self::INVALID_MIME_TYPE, array('%types%'=>substr($validTypes, 0, -2))));
			return;
		}
	}
	
	public function setRequired($required)
	{
		$this->_required = $required;
		
		return $this;
	}
	
	public function addRequiredMimeType($type)
	{
		$this->_mimeTypes[] = $type;
		return $this;
	}
}