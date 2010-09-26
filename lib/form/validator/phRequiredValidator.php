<?php
require_once 'validator/phValidatorCommon.php';
require_once 'validator/phValidatorException.php';
/**
 * A validator that makes sure something was entered
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage validator
 */
class phRequiredValidator extends phValidatorCommon
{
	const REQUIRED = 1;
	
	public function validate(phValidatableFormDataItem $item)
	{
		$value = $item->getValue();
		if(is_array($value))
		{
			throw new phValidatorException('I cannot validate elements that return multiple values');
		}
		
		$valid = (strlen($value)>0);
		if(!$valid)
		{
			$item->addError($this->getError(self::REQUIRED));
		}
		
		return $valid;
	}
	
	protected function getValidErrorCodes()
	{
		return array(self::REQUIRED);
	}
	
	protected function getDefaultErrorMessages()
	{
		return array(self::REQUIRED=>'This is a required field, please enter a value');
	}
}