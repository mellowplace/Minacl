<?php
require_once('element/phInputElement.php');
/**
 * This class provides handling for checkboxes 
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage element
 */
class phcheckboxElement extends phInputElement
{
	public function setValue($value)
	{
		$e = $this->getElement();
		
		if($value==$this->getRawValue())
		{
			/*
			 * value being set to same as our elements
			 * value="" attribute, therefore we need to
			 * be marked as checked
			 */
			unset($e->attributes()->checked);
			$e->addAttribute('checked','checked');
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
