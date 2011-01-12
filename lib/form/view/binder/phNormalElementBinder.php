<?php
/**
 * This class can bind normal elements in a view i.e. not forms
 * and not arrays
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage view.binder
 */
class phNormalElementBinder extends phFormViewElementBinder
{
	/**
	 * (non-PHPdoc)
	 * @see lib/form/view/phFormViewElementBinder::canBindFor()
	 */
	public function canBindFor(phNameInfo $name, phForm $form)
	{
		return (!$form->hasForm($name->getName()) && !$name->isArray());
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/view/phFormViewElementBinder::createAndBindDataItems()
	 */
	public function createAndBindDataItems($elements, phNameInfo $name, phForm $form)
	{
		$dataItem = new phFormDataItem($name->getName());
		foreach($elements as $e)
		{
			$e->bindDataItem($dataItem);
		}
		
		return $dataItem;
	}
}