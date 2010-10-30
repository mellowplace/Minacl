<?php
/**
 * This represents some data, it can have values bound to it and 
 * can be cleared of data.  It can also be validated.
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 */
interface phData extends phValidatable
{
	/**
	 * @return string a name to identify this data with
	 */
	public function getName();
	
	/**
	 * Binds a value to the data
	 * @param $values mixed the data to be bound
	 */
	public function bind($values);
	
	/**
	 * clears any bound values
	 */
	public function clear();
}