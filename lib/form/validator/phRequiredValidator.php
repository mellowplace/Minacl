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
	protected function doValidate(phValidatableFormDataItem $item)
	{
		$value = $item->getValue();
		if(is_array($value))
		{
			throw new phValidatorException('I cannot validate elements that return multiple values');
		}
		
		return (strlen($value)>0);
	}
}