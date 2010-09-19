<?php
require_once 'validator/phValidator.php';
/**
 * Common base for a basic validator that provides an error message
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage validator
 */
abstract class phValidatorCommon implements phValidator
{
	protected $_errorMessage = null;
	
	public function __construct($errorMessage)
	{
		$this->_errorMessage = $errorMessage;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/validator/phValidator::validate()
	 */
	public function validate(phElement $checkElement, phForm $bindingForm)
	{
		$ok = $this->doValidate($checkElement, $bindingForm);
		if(!$ok)
		{
			$checkElement->addError($this->_errorMessage);
		}
		return $ok;
	}
	
	protected abstract function doValidate(phElement $checkElement, phForm $bindingForm);
}