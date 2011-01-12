<?php
/**
 * This class can parse name strings
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage view
 */
class phNameInfo
{
	protected $_name = null;
	protected $_array = false;
	protected $_arrayKeyString = null;
	protected $_valid = false;
	
	public function __construct($nameString)
	{
		$numMatched = preg_match('/^([a-zA-Z\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*?)((\[[a-zA-Z0-9_\x7f-\xff]*?\])*)?$/', $nameString, $matches);
		
		if($numMatched!==0)
		{
			$name = $matches[1];
			$array = false;
			$arrayParts = '';
			
			if(isset($matches[2]) && strlen($matches[2]) > 0)
			{
				$array = true;
				$arrayParts = $matches[2];
			}
			
			$this->_name = $name;
			$this->_arrayKeyString = $arrayParts;
			$this->_array = $array;
			$this->_valid = true;
		}
	}
	
	public function getName()
	{
		return $this->_name;
	}
	
	public function isArray()
	{
		return $this->_array;
	}
	
	public function getArrayKeyString()
	{
		return $this->_arrayKeyString;
	}
	
	public function isValid()
	{
		return $this->_valid;
	}
}