<?php
/**
 * The phElement interface allows us to specify a common interface for handling user
 * input, it can have data bound to it, be validated and clean input.
 *  
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 */
interface phElement
{
	/**
	 * Allows us to bind a value to the object
	 * @param mixed $value
	 * @param phForm $form the form that is being bound
	 */
	public function bind($value, phForm $form = null);
	
	/**
	 * @return boolean true if the bound value is valid
	 */
	public function isValid();
	
	/**
	 * Gets cleaned values for the element
	 * 
	 * @return mixed
	 */
	public function getValues();
	
	/**
	 * Clears any given value(s) in this element
	 */
	public function clearValues();
	
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