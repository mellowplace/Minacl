<?php
/**
 * the validator interface allows us to validate an element
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage validator
 */
interface phValidator
{
	/**
	 * validates $item's input
	 * 
	 * @param $item phValidatableFormDataItem the data item being validated
	 * @return boolean true if the content is valid, false otherwise
	 */
	public function validate(phValidatableFormDataItem $item);
}