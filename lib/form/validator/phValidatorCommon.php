<?php
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
	public abstract function validate(phElement $checkElement, phForm $bindingForm);
}