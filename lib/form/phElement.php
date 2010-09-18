<?php
/**
 * A phElement is a decorator for SimpleXMLElement objects that allows a html tag like "input"
 * to be an element in a phForm
 * 
 * @author Rob Graham <rob@mellowplace.com>
 */
abstract class phElement
{
	/**
	 * An optional validator class that checks the value for this element is valid
	 * @var phValidator
	 */
	protected $_validator = null;
	/**
	 * The actual html element we are decorating
	 * @var SimpleXMLElement
	 */
	protected $_element = null;
	
	public function __construct(phForm $form, SimpleXMLElement $element)
	{
		
	}
	
	public function setValidator(phValidator $validator)
	{
		$this->_validator = $validator;
	}
	
	public function isValid()
	{
		return ($this->_validator===null || $this->_validator->isValid($this));
	}
	
	public abstract function getValue();
}