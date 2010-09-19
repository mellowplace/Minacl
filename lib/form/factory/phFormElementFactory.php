<?php
require_once('element/phInputElement.php');
require_once('factory/phElementFactory.php');
/**
 * This factory can handle the phform element
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage factory
 */
class phFormElementFactory extends phElementFactory
{
	public function canHandle(SimpleXMLElement $e)
	{
		return $e->getName()=='phform';
	}
	
	public function createPhElement(SimpleXMLElement $e, phFormView $view)
	{
		return $view->getForm()->getForm(
			$view->getRealId((string)$e->attributes()->id)
		);
	}
}