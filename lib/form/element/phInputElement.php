<?php
require_once('element/phSimpleXmlElement.php');

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
