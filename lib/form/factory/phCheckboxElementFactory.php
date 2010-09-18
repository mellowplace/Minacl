<?php
require_once('element/phCheckboxElement.php');
require_once('phElementFactory.php');
/**
 * This factory can handle checkbox elements
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage factory
 */
class phCheckboxElementFactory extends phElementFactory
{
	public function canHandle(SimpleXMLElement $e)
	{
		if($e->getName()=='input')
		{
			$attributes = $e->attributes();
			foreach($attributes as $name=>$value)
			{
				if($name=='type' && $value=='checkbox')
				{
					return true;
				}
			}
		}
		return false;
	}
	
	public function createPhElement(SimpleXMLElement $e, phForm $form)
	{
		return new phCheckboxElement($e, $form);
	}
}