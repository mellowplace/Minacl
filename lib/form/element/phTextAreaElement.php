<?php
/**
 * This class provides handling for the textarea element
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage element
 */
class phTextAreaElement extends phSimpleXmlElement
{
	public function getRawValue()
	{
		$e = $this->getElement();
		return (string)$e;
	}
	
	public function setRawValue($value)
	{
		$e = $this->getElement();
		$e[0] = (string)$value;
	}
}
