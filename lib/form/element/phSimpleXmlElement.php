<?php
require_once 'phFormViewElement.php';
require_once 'phDataChangeListener.php';
require_once 'validator/phValidator.php';
/**
 * A phSimpleXmlElement is a decorator for SimpleXMLElement objects that allows a html tag like "input"
 * to be an element in a phForm
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage element
 */
abstract class phSimpleXmlElement implements phDataChangeListener, phFormViewElement
{
	/**
	 * The view this element appears on
	 * @var phFormView
	 */
	protected $_view = null;
	
	public function __construct(SimpleXMLElement $element, phFormView $view)
	{
		$this->_element = $element;
		$this->_view = $view;
	}
	
	public function setValue($value)
	{
		$this->setRawValue($value);
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
	
	/**
	 * @return SimpleXMLElement
	 */
	public function getElement()
	{
		return $this->_element;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phDataChangeListener::dataChanged()
	 */
	public function dataChanged(phFormDataItem $item)
	{
		$this->setValue($item->getValue());
	}
}