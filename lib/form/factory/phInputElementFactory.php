<?php
require_once('element/phInputElement.php');
require_once('phElementFactory.php');
/**
 * This factory can handle all input elements bar if they are a checkbox
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage factory
 */
class phInputElementFactory extends phElementFactory
{
	public function canHandle(SimpleXMLElement $e)
	{
		if($e->getName()=='input')
		{
			$attributes = $e->attributes();
			foreach($attributes as $name=>$value)
			{
				if($name=='type' && $value!='checkbox')
				{
					return true;
				}
			}
		}
		return false;
	}
	
	public function createPhElement(SimpleXMLElement $e, phForm $form)
	{
		return new phInputElement($e, $form);
	}
}