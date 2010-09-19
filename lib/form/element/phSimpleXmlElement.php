<?php
require_once 'phElement.php';
/**
 * A phSimpleXmlElement is a decorator for SimpleXMLElement objects that allows a html tag like "input"
 * to be an element in a phForm
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 */
abstract class phSimpleXmlElement implements phElement
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
	/**
	 * A cleaner that if set will clean the user inputted data
	 * @var phCleaner
	 */
	protected $_cleaner = null;
	
	public function __construct(SimpleXMLElement $element, phFormView $view)
	{
		$this->_element = $element;
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
	
	public function isValid()
	{
		return ($this->_validator===null || $this->_validator->isValid($this));
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
	
	public function setValue($value)
	{
		$this->setRawValue($value);
	}
	
	/**
	 * Gets a value from the element that has been cleaned by its data cleaner
	 * @return mixed
	 */
	public function getValues()
	{
		return $this->_cleaner!==null ?
					$this->getCleaner()->clean($this->getRawValue()) : $this->getRawValue();
	}
	
	public function clearValues()
	{
		$this->setValue(null);
	}
	
	/**
	 * gets the raw value of the SimpleXmlElement element
	 */
	public abstract function getRawValue();
	
	/**
	 * sets the raw value of the SimpleXmlElement element
	 * @param mixed $value
	 */
	public abstract function setRawValue($value);
	
	public function bind($value)
	{
		$this->setValue($value);
	}
	
	public function getElement()
	{
		return $this->_element;
	}
}