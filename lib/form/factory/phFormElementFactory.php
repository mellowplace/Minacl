<?php
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
		$form = $view->getForm()->getForm(
			$view->getRealName((string)$e->attributes()->name)
		);
		return $form;
	}
}