<?php
require_once('element/phInputElement.php');

class phcheckboxElement extends phInputElement
{
	public function setValue($value)
	{
		$e = $this->getElement();
		
		if($value==$this->getRawValue())
		{
			/*
			 * value being set to same as our elements
			 * value="" attribute, therefor we need to
			 * be marked as checked
			 */
			$e->attributes()->checked = "checked";
		}
		else
		{
			/*
			 * make sure we are not checked
			 */
			unset($e->attributes()->checked);
		}
	}
}
