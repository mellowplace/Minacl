<?php
/**
 * This class can bind form elements in a view
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage view.binder
 */
class phFormElementBinder extends phFormViewElementBinder
{
	/**
	 * (non-PHPdoc)
	 * @see lib/form/view/phFormViewElementBinder::canBindFor()
	 */
	public function canBindFor(phNameInfo $name, phForm $form)
	{
		return $form->hasForm($name->getName());
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/view/phFormViewElementBinder::createAndBindDataItems()
	 */
	public function createAndBindDataItems($elements, phNameInfo $name, phForm $form)
	{
		return $form->getForm($name->getName());
	}
}