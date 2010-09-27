<?php
require_once('phData.php');
/**
 * A single item of data that can be read and cleaned
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 */
class phFormDataItem implements phData
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
	
	/**
	 * An optional validator class that checks the value for this element is valid
	 * @var phValidator
	 */
	protected $_validator = null;
	/**
	 * when the element is bound the validator will run and set this variable
	 * @var boolean
	 */
	protected $_valid = true;
	/**
	 * Holds a list of errors that validators may attach
	 * @var array
	 */
	protected $_errors = array();
	

	public function __construct($name)
	{
		$this->_name = $name;
	}
	
	public function setValidator(phValidator $validator)
	{
		$this->_validator = $validator;
	}
	
	/**
	 * @return phValidator
	 */
	public function getValidator()
	{
		return $this->_validator;
	}
	
	public function validate()
	{
		if($this->_validator!==null)
		{
			return $this->_validator->validate($this->getValue(), $this);
		}
		
		return true;
	}
	
	/**
	 * Gets the name of this element as it appears in the view
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * allows a validator to attach an error message to this item of data
	 * @param unknown_type $message
	 */
	public function addError($message)
	{
		$this->_errors[] = $message;
	}
	
	/**
	 * resets any error messages this data item might have
	 */
	public function resetErrors()
	{
		$this->_errors = array();
	}
	
	/**
	 * gets any error messages that have been added to this data item
	 * @return array
	 */
	public function getErrors()
	{
		return $this->_errors;
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
	public function clear()
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
		
	public function addChangeListener(phDataChangeListener $l)
	{
		$this->_listeners[] = $l;
	}
}