<?php
/**
 * This class can bind elements in a view that have an array style name
 * e.g. <input type="hidden" name="ids[]" ...
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage view.binder
 */
class phArrayDataElementBinder extends phFormViewElementBinder
{
	/**
	 * @var array an array of phFormDataItem objects that I have created
	 */
	protected $_dataItems = array();
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/view/phFormViewElementBinder::canBindFor()
	 */
	public function canBindFor(phNameInfo $name, phForm $form)
	{
		return $name->isArray();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/view/phFormViewElementBinder::createAndBindDataItems()
	 */
	public function createAndBindDataItems($elements, phNameInfo $name, phForm $form)
	{
		if(array_key_exists($name->getName(), $this->_dataItems))
		{
			$dataItem = $this->_dataItems[$name->getName()];
		}
		else
		{
			$dataItem = new phArrayFormDataItem($name->getName());
			$this->_dataItems[$name->getName()] = $dataItem;
		}
		
		foreach($elements as $e)
		{
			$d = $dataItem->registerArrayKeyString($name->getArrayKeyString());
			$e->bindDataItem($d);
		}
		
		return $dataItem;
	}
}