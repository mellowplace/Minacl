<?php
class phFormDataItem
{
/**
	 * The name of this item of data
	 * 
	 * @var string
	 */
	protected $_name = null;
	/**
	 * A cleaner that if set will clean the user inputted data
	 * @var phCleaner
	 */
	protected $_cleaner = null;
	/**
	 * The value of the data item
	 * @var string
	 */
	protected $_value = null;
	/**
	 * an array of data change listeners who wish to be notified of changes
	 * to this items data
	 * @var array
	 */
	protected $_listeners = array();
	
	public function __construct($name)
	{
		$this->_name = $name;
	}
	
	public function setCleaner(phCleaner $cleaner)
	{
		$this->_cleaner = $cleaner;
	}
	
	/**
	 * @return phCleaner an object who knows how to clean the elements data
	 */
	public function getCleaner()
	{
		return $this->_cleaner;
	}
	
	/**
	 * Gets cleaned values for the element
	 * 
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->_value;
	}
	
	/**
	 * Clears any given value(s) in this element
	 */
	public function clearValue()
	{
		$this->bind(null);
	}
	
	/**
	 * Binds $value to this data item and notifies the listeners of
	 * a change
	 * 
	 * @param mixed $value
	 */
	public function bind($value)
	{
		$this->_value = $value;
		
		foreach($this->_listeners as $l)
		{
			$l->dataChanged($this);
		}
	}
	
	/**
	 * Gets the name of this element as it appears in the view
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	public function addChangeListener(phDataChangeListener $l)
	{
		$this->_listeners[] = $l;
	}
}