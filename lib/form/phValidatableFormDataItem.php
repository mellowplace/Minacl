<?php
require_once('phFormDataItem.php');
require_once('phValidatable.php');

/**
 * The phValidatableFormDataItem class allows us to specify a common interface for handling user
 * input, it can have data bound to it, be validated and clean input.
 *  
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 */
class phValidatableFormDataItem extends phFormDataItem implements phValidatable
{
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
			return $this->_validator->validate($this);
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
}