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
	 * @param $value mixed the value being validated
	 * @param $errors phErrorable the object to attach errors to
	 * @return boolean true if the content is valid, false otherwise
	 */
	public function validate($value, phValidatable $errors);
}