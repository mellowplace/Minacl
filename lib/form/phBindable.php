<?php
/**
 * The bindable interface allows us to specify a method for binding a posted
 * value to an object, it is used by phForm and phElement
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 */
interface phBindable
{
	/**
	 * Allows us to bind a value to the object
	 * @param mixed $value
	 */
	public function bind($value);
}