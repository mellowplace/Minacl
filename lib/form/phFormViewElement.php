<?php
/**
 * This class provides a common definition for phForm and phSimpleXmlElement objects
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 */
interface phFormViewElement
{
	/**
	 * This method binds the appropriate phFormDataItem to the element
	 * @param phFormDataItem $item
	 * @return void
	 */
	public function bindDataItem(phFormDataItem $item);
}