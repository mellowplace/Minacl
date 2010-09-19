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
	 * validates $checkElement's input
	 * 
	 * @param $checkElement
	 * @param $bindingForm the phForm object that is being bound with user input
	 * @return boolean true if the content is valid, false otherwise
	 */
	public function validate(phElement $checkElement, phForm $bindingForm);
}