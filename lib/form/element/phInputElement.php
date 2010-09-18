<?php
require_once('phElement.php');

class phInputElement extends phElement
{
	public function getRawValue()
	{
		$e = $this->getElement();
		return $e->attributes()->value;
	}
	
	public function setRawValue($value)
	{
		$e = $this->getElement();
		$e->attributes()->value = $value;
	}
}
