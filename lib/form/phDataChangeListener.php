<?php
/**
 * A data change listener provides a way for an object to recieve a notification when the value
 * of a phFormDataItem changes
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 */
interface phDataChangeListener
{
	/**
	 * called when the data in a phFormDataItem changes
	 * 
	 * @param phFormDataItem $item the item in which data changed
	 */
	public function dataChanged(phFormDataItem $item);
}