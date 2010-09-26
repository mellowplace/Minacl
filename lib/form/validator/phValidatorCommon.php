<?php
require_once 'validator/phValidator.php';
/**
 * Common base for a basic validator that provides error messages
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage validator
 */
abstract class phValidatorCommon implements phValidator
{
	protected $_errors = array();
	
	public function __construct(array $errors = array())
	{
		foreach($errors as $code=>$e)
		{
			$this->setError($code, $e);
		}
	}
	
	public function setError($code, $message)
	{
		if(!in_array($code, $this->getValidErrorCodes()))
		{
			throw new phValidatorException("The error code '{$code}' is not valid for this validator");
		}
		
		$this->_errors[$code] = $message;
	}
	
	protected function getError($code)
	{
		if(isset($this->_errors[$code]))
		{
			return $this->_errors[$code];
		}
		
		$defaults = $this->getDefaultErrorMessages();
		return $defaults[$code];
	}
	
	protected abstract function getValidErrorCodes();
	
	protected abstract function getDefaultErrorMessages();
}