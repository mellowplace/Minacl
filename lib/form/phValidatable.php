<?php
/**
 * Something that is "validatable" can be validated by a phValidator
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 *
 */
interface phValidatable
{	
	public function validate();
	/**
	 * allows a validator to attach an error message to this element
	 * @param unknown_type $message
	 */
	public function addError($message);
	
	/**
	 * resets any error messages this element might have
	 */
	public function resetErrors();
	
	/**
	 * gets any error messages that have been added to this element
	 * @return array
	 */
	public function getErrors();
}