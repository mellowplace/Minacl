<?php
/**
 * The phElement interface allows us to specify a common interface for handling user
 * input, it can have data bound to it, validated and cleaned.
 *  
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 */
interface phElement
{
	/**
	 * Allows us to bind a value to the object
	 * @param mixed $value
	 */
	public function bind($value);
	
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
}