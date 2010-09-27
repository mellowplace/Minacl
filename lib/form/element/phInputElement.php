<?php
require_once('element/phSimpleXmlElement.php');
/**
 * This class provides handling for basic "input" elements 
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage element
 */
class phInputElement extends phSimpleXmlElement
{
	public function getRawValue()
	{
		$e = $this->getElement();
		return (string)$e->attributes()->value;
	}
	
	public function setRawValue($value)
	{
		$e = $this->getElement();
		$e->attributes()->value = (string)$value;
	}
}
