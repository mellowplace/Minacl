<?php
/**
 * This factory can handle textarea elements
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage factory
 */
class phTextAreaElementFactory extends phElementFactory
{
	public function canHandle(SimpleXMLElement $e)
	{
		return ($e->getName()=='textarea');
	}
	
	public function createPhElement(SimpleXMLElement $e, phFormView $view)
	{
		return new phTextAreaElement($e, $view);
	}
}